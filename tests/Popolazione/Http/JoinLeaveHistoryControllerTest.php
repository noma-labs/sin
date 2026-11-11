<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Carbon\Carbon;

it('shows the entrate and uscite of the person in a timeline from the most recent', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-15', 'data_uscita' => '1995-07-20']);
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), GruppoFamiliare::all()->random());

    $this->get(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Presente in Nomadelfia',
            'Rientra in Nomadelfia', '2010', '14 anni 7 mesi',
            'Esce da Nomadelfia', '1995', '5 anni 6 mesi',
            'Entra in Nomadelfia', '1990',
            'Nascita', '1970',
        ])
        ->assertSee(route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '2010-03-01']))
        ->assertSee(route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '1990-01-15']))
        ->assertSee(route('nomadelfia.leave.update', ['id' => $persona->id, 'uscita' => '1995-07-20']))
        ->assertSee(route('nomadelfia.join-leave-history.destroy', ['id' => $persona->id, 'entrata' => '1990-01-15']))
        ->assertDontSee(route('nomadelfia.join-leave-history.destroy', ['id' => $persona->id, 'entrata' => '2010-03-01']));
});

it('shows before saving a correction the posizioni, gruppi and aziende that move with the date', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2000-01-01'), $gruppo);
    $azienda = Azienda::factory()->create();
    app(AssegnaAziendaAction::class)->execute($persona, $azienda, Carbon::parse('2001-02-01'), 'LAVORATORE');
    app(UscitaPersonaAction::class)->execute($persona, Carbon::parse('2005-01-01'));

    $this->get(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Modifica data uscita', 'Verranno spostate alla nuova data anche',
            'Posizione', 'Ospite', 'dal 1 gen 2000', 'al 1 gen 2005',
            'Gruppo familiare', $gruppo->nome, 'dal 1 gen 2000', 'al 1 gen 2005',
            'Azienda', $azienda->nome_azienda, 'dal 1 feb 2001', 'al 1 gen 2005',
            'Modifica data entrata', 'Verranno spostate alla nuova data anche',
            'Posizione', 'Ospite', 'dal 1 gen 2000',
            'Gruppo familiare', $gruppo->nome,
        ]);
});

it('says that only the date changes when nothing started or ended with it', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1930-04-10'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1955-06-01', 'data_uscita' => '1972-09-30']);

    $this->get(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSuccessful()
        ->assertSee('cambia solo la data di uscita.')
        ->assertSee('cambia solo la data di entrata.')
        ->assertDontSee('Verranno spostate alla nuova data anche');
});

it('offers to remove only the last data di uscita, showing the posizione and the gruppo that are reopened', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-01', 'data_uscita' => '1995-12-31']);
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2000-01-01'), $gruppo);
    app(UscitaPersonaAction::class)->execute($persona, Carbon::parse('2005-01-01'));

    $this->get(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSuccessful()
        ->assertSee("formEliminaDataUscita{$persona->id}-2005-01-01")
        ->assertDontSee("formEliminaDataUscita{$persona->id}-1995-12-31")
        ->assertSeeInOrder(['risulterà presente in Nomadelfia', 'Verranno riaperte anche', 'Posizione', 'Ospite', 'Gruppo familiare', $gruppo->nome]);
});

it('does not offer to remove the data di uscita of a deceased person', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1930-04-10'))->maschio()->create(['data_decesso' => '2001-02-03']);
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1960-01-01', 'data_uscita' => '2001-02-03']);

    $this->get(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSuccessful()
        ->assertDontSee('Elimina data uscita');
});

it('records a past period in Nomadelfia of a person who is not in the popolazione', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1930-04-10'))->maschio()->create(['data_decesso' => '2001-02-03']);

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), [
        'data_entrata' => '1955-06-01',
        'data_uscita' => '1972-09-30',
    ])
        ->assertRedirect(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('popolazione', ['persona_id' => $persona->id, 'data_entrata' => '1955-06-01', 'data_uscita' => '1972-09-30'], 'db_nomadelfia');
    expect($persona->isPersonaInterna())->toBeFalse();
    $this->get(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSeeInOrder(['Esce da Nomadelfia', '1972', 'Entra in Nomadelfia', '1955']);
});

it('records a past period of a person who lives in Nomadelfia without changing the current presence', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), GruppoFamiliare::all()->random());

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), [
        'data_entrata' => '1990-01-15',
        'data_uscita' => '1995-07-20',
    ])->assertSessionHasNoErrors();

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->orderBy('data_entrata')->get(['data_entrata', 'data_uscita'])->toArray())
        ->toBe([
            ['data_entrata' => '1990-01-15', 'data_uscita' => '1995-07-20'],
            ['data_entrata' => '2010-03-01', 'data_uscita' => null],
        ]);
});

it('rejects a past period overlapping another period of the person', function (string $dataEntrata, string $dataUscita, string $errore): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-01', 'data_uscita' => '1995-12-31']);
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), GruppoFamiliare::all()->random());

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), [
        'data_entrata' => $dataEntrata,
        'data_uscita' => $dataUscita,
    ])->assertSessionHasErrors(['data_uscita' => $errore]);

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->count())->toBe(2);
})->with([
    'ends inside a period of the storico' => ['1988-01-01', '1991-01-01', 'Il periodo si sovrappone a quello dal 1990-01-01 al 1995-12-31.'],
    'contains a period of the storico' => ['1989-01-01', '1997-01-01', 'Il periodo si sovrappone a quello dal 1990-01-01 al 1995-12-31.'],
    'ends after the current entrata' => ['2005-01-01', '2012-01-01', 'Il periodo si sovrappone alla presenza attuale in Nomadelfia, iniziata il 2010-03-01.'],
]);

it('rejects a past period with the data di uscita before the data di entrata', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), [
        'data_entrata' => '1995-01-01',
        'data_uscita' => '1990-01-01',
    ])->assertSessionHasErrors(['data_uscita' => 'La data di uscita non può essere precedente alla data di entrata.']);
});

it('rejects a past period starting before the birth of the person', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), [
        'data_entrata' => '1960-01-01',
        'data_uscita' => '1990-01-01',
    ])->assertSessionHasErrors(['data_entrata' => 'La data di entrata non può essere precedente alla data di nascita.']);
});

it('rejects a past period ending in the future or after the death of the person', function (?string $dataDecesso, string $dataUscita): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1930-04-10'))->maschio()->create(['data_decesso' => $dataDecesso]);

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), [
        'data_entrata' => '1960-01-01',
        'data_uscita' => $dataUscita,
    ])->assertSessionHasErrors(['data_uscita' => 'La data di uscita non può essere futura o successiva alla data di decesso.']);
})->with([
    'in the future' => [null, Carbon::now()->addDay()->toDateString()],
    'after the death' => ['2001-02-03', '2001-02-04'],
]);

it('requires the data di entrata of a period, but not the data di uscita of a living person', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), [])
        ->assertSessionHasErrors(['data_entrata' => 'La data di entrata è obbligatoria.'])
        ->assertSessionDoesntHaveErrors('data_uscita');
});

it('records a period without data di uscita after the last uscita, putting the person in the popolazione attuale', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1940-04-10'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1960-01-01', 'data_uscita' => '1970-12-31']);

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), ['data_entrata' => '1980-03-01', 'data_uscita' => ''])
        ->assertRedirect(route('nomadelfia.join-leave-history.index', $persona->id))
        ->assertSessionHas('success', "Periodo di {$persona->nominativo} inserito senza data di uscita: risulta presente in Nomadelfia.");

    $this->assertDatabaseHas('popolazione', ['persona_id' => $persona->id, 'data_entrata' => '1980-03-01', 'data_uscita' => null], 'db_nomadelfia');
    expect($persona->isPersonaInterna())->toBeTrue();
});

it('rejects a period without data di uscita that is not after the last uscita', function (string $dataEntrata, string $errore): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1940-04-10'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1960-01-01', 'data_uscita' => '1970-12-31']);

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), ['data_entrata' => $dataEntrata])
        ->assertSessionHasErrors(['data_entrata' => $errore]);

    expect($persona->isPersonaInterna())->toBeFalse();
})->with([
    'before the last uscita' => ['1965-01-01', 'Il periodo si sovrappone a quello dal 1960-01-01 al 1970-12-31.'],
    'before the first entrata' => ['1950-01-01', 'Il periodo si sovrappone a quello dal 1960-01-01 al 1970-12-31.'],
    'in the future' => [Carbon::now()->addDay()->toDateString(), 'La data di entrata non può essere futura.'],
]);

it('rejects a period without data di uscita for a person already present in Nomadelfia', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), GruppoFamiliare::all()->random());

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), ['data_entrata' => '2015-01-01'])
        ->assertSessionHasErrors(['data_entrata' => 'Il periodo si sovrappone alla presenza attuale in Nomadelfia, iniziata il 2010-03-01.']);

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->count())->toBe(1);
});

it('requires the data di uscita of a period of a deceased person', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1930-04-10'))->maschio()->create(['data_decesso' => '2001-02-03']);

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), ['data_entrata' => '1960-01-01'])
        ->assertSessionHasErrors(['data_uscita' => 'La data di uscita è obbligatoria per una persona deceduta.']);

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->exists())->toBeFalse();
});

it('forbids recording a past period to a user without the permission to insert in the popolazione', function (): void {
    $user = User::query()->create(['username' => 'senza-permessi-storico', 'email' => 'senza-permessi-storico@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(route('nomadelfia.join-leave-history.store', $persona->id), [
        'data_entrata' => '1990-01-01',
        'data_uscita' => '1995-01-01',
    ])->assertForbidden();

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->exists())->toBeFalse();
});

it('deletes a period of the storico recorded with wrong dates', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert([
        ['persona_id' => $persona->id, 'data_entrata' => '1990-01-15', 'data_uscita' => '1995-07-20'],
        ['persona_id' => $persona->id, 'data_entrata' => '2000-01-01', 'data_uscita' => '2005-01-01'],
    ]);

    $this->delete(route('nomadelfia.join-leave-history.destroy', ['id' => $persona->id, 'entrata' => '1990-01-15']))
        ->assertRedirect(route('nomadelfia.join-leave-history.index', $persona->id));

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->pluck('data_entrata')->all())->toBe(['2000-01-01']);
});

it('does not delete the current presence in Nomadelfia of the person', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), GruppoFamiliare::all()->random());

    $this->delete(route('nomadelfia.join-leave-history.destroy', ['id' => $persona->id, 'entrata' => '2010-03-01']))
        ->assertNotFound();

    expect($persona->isPersonaInterna())->toBeTrue();
});

it('forbids deleting a period to a user without the permission to modify the popolazione', function (): void {
    $user = User::query()->create(['username' => 'senza-permessi-elimina', 'email' => 'senza-permessi-elimina@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-15', 'data_uscita' => '1995-07-20']);

    $this->delete(route('nomadelfia.join-leave-history.destroy', ['id' => $persona->id, 'entrata' => '1990-01-15']))
        ->assertForbidden();

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->exists())->toBeTrue();
});
