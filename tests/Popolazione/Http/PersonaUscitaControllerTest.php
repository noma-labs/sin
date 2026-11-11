<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Incarico\Models\Incarico;
use App\Nomadelfia\Persona\Controllers\LeaveCommunityController;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaIncaricoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Carbon\Carbon;

it('exit a persona', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, Carbon::now()->startOfDay(), GruppoFamiliare::all()->random());

    login();

    $data_uscita = Carbon::now()->startOfDay();

    $this->post(action([LeaveCommunityController::class, 'store'], $persona->id),
        [
            'data_uscita' => $data_uscita->toDateString(),
        ])
        ->assertRedirect()
        ->assertRedirectContains(route('nomadelfia.person.profile', $persona->id));

    $p = Persona::findOrFail($persona->id);
    expect($p->getDataUscitaNomadelfia())->toEqual($data_uscita)
        ->and($p->isPersonaInterna())->toBeFalse();
});

it('corrects the data di uscita of a period in the storico', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1960-01-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-01', 'data_uscita' => '1995-12-31']);

    $this->post(route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '1995-12-31']), ['data_uscita' => '1994-03-10'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->get(['data_entrata', 'data_uscita'])->toArray())
        ->toBe([['data_entrata' => '1990-01-01', 'data_uscita' => '1994-03-10']]);
});

it('rejects a data di uscita that is not a valid correction of the period', function (string $dataUscita, string $errore): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1960-01-01'))->maschio()->create(['data_decesso' => '2020-05-05']);
    PopolazioneNomadelfia::query()->insert([
        ['persona_id' => $persona->id, 'data_entrata' => '1990-01-01', 'data_uscita' => '1995-12-31'],
        ['persona_id' => $persona->id, 'data_entrata' => '2000-01-01', 'data_uscita' => '2005-12-31'],
    ]);

    $this->post(route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '1995-12-31']), ['data_uscita' => $dataUscita])
        ->assertSessionHasErrors(['data_uscita' => $errore]);

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->where('data_uscita', '1995-12-31')->exists())->toBeTrue();
})->with([
    'empty, which would reopen the period' => ['', 'La data di uscita è obbligatoria.'],
    'before the data di entrata' => ['1989-12-31', 'La data di uscita non può essere precedente alla data di entrata.'],
    'after the decesso' => ['2020-05-06', 'La data di uscita non può essere futura o successiva alla data di decesso.'],
    'overlapping another period' => ['2001-01-01', 'Il periodo si sovrappone a quello dal 2000-01-01 al 2005-12-31.'],
]);

it('rejects a data di uscita overlapping the current presence in Nomadelfia', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1960-01-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-01', 'data_uscita' => '1995-12-31']);
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), GruppoFamiliare::all()->random());

    $this->post(route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '1995-12-31']), ['data_uscita' => '2011-01-01'])
        ->assertSessionHasErrors(['data_uscita' => 'Il periodo si sovrappone alla presenza attuale in Nomadelfia, iniziata il 2010-03-01.']);
});

it('moves what the uscita concluded to the corrected data di uscita', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2000-01-01'), GruppoFamiliare::all()->random());
    app(AssegnaAziendaAction::class)->execute($persona, Azienda::factory()->create(), Carbon::parse('2001-01-01'), 'LAVORATORE');
    (new AssegnaIncaricoAction)->execute($persona, Incarico::factory()->create(), Carbon::parse('2002-01-01'));
    app(UscitaPersonaAction::class)->execute($persona, Carbon::parse('2005-01-01'));

    $this->post(route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '2005-01-01']), ['data_uscita' => '2004-06-30'])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('popolazione', ['persona_id' => $persona->id, 'data_entrata' => '2000-01-01', 'data_uscita' => '2004-06-30'], 'db_nomadelfia');
    $this->assertDatabaseHas('persone_posizioni', ['persona_id' => $persona->id, 'data_inizio' => '2000-01-01', 'data_fine' => '2004-06-30'], 'db_nomadelfia');
    $this->assertDatabaseHas('gruppi_persone', ['persona_id' => $persona->id, 'data_entrata_gruppo' => '2000-01-01', 'data_uscita_gruppo' => '2004-06-30'], 'db_nomadelfia');
    $this->assertDatabaseHas('aziende_persone', ['persona_id' => $persona->id, 'data_inizio_azienda' => '2001-01-01', 'data_fine_azienda' => '2004-06-30'], 'db_nomadelfia');
    $this->assertDatabaseHas('incarichi_persone', ['persona_id' => $persona->id, 'data_inizio' => '2002-01-01', 'data_fine' => '2004-06-30'], 'db_nomadelfia');
});

it('rejects a data di uscita before the start of an azienda that the uscita concluded', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2000-01-01'), GruppoFamiliare::all()->random());
    $azienda = Azienda::factory()->create();
    app(AssegnaAziendaAction::class)->execute($persona, $azienda, Carbon::parse('2004-09-01'), 'LAVORATORE');
    app(UscitaPersonaAction::class)->execute($persona, Carbon::parse('2005-01-01'));

    $this->post(route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '2005-01-01']), ['data_uscita' => '2004-06-30'])
        ->assertSessionHasErrors(['data_uscita' => "La data di uscita non può essere precedente all'inizio di Azienda: {$azienda->nome_azienda} (2004-09-01)."]);

    $this->assertDatabaseHas('popolazione', ['persona_id' => $persona->id, 'data_uscita' => '2005-01-01'], 'db_nomadelfia');
});
