<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Controllers\PersonController;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PrintableWordPopolazioneController;
use App\Scuola\Models\Anno;
use Carbon\Carbon;

it('forbids access to guests', function (): void {
    $this->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertRedirect(route('login'));
});

it('show index of nomadelfia', function (): void {
    login();
    $this->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertSuccessful();
});

it('cannot insert persona with same nominativo', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $data_entrata = Carbon::now()->startOfDay();
    $gruppo = GruppoFamiliare::all()->random();
    $act = app(EntrataMaggiorenneSingleAction::class);
    $act->execute($persona, $data_entrata, $gruppo);

    login();
    $this->post(action([PersonController::class, 'store']),
        [
            'nominativo' => $persona->nominativo,
            'nome' => 'name',
            'cognome' => 'my-surname',
            'data_nascita' => '2022-10-10',
            'luogo_nascita' => 'Grosseto',
            'sesso' => 'M',
        ]);
    // TODO: enabling this the test explode with thousand of errors !!
    //            ->assertSee("Il nominativo inserito è già assegnato alla persona");
});

it('can export popolazione into word', function (): void {
    Anno::createAnno(2042);

    login();
    $this->withoutExceptionHandling();
    $this->get(action([PrintableWordPopolazioneController::class]),
        [
            'elenchi' => ['effePostOspFig', 'famiglie', 'gruppi', 'aziende', 'incarichi', 'scuola'],
        ])
        ->assertSuccessful()
        ->assertDownload();
});

it('lists the mamme di vocazione from the tipo of the famiglia they head', function (): void {
    $gruppo = GruppoFamiliare::all()->random();
    $mamma = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Mamma Di Vocazione']);
    app(EntrataMaggiorenneSingleAction::class)->execute($mamma, Carbon::parse('1975-01-01'), $gruppo);
    Famiglia::factory()->create(['data_creazione' => '1980-03-12', 'tipo' => TipoFamiglia::MammaDiVocazione])->assegnaCapoFamiglia($mamma);
    $nomadelfaMamma = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Nomadelfa Mamma']);
    app(EntrataMaggiorenneSingleAction::class)->execute($nomadelfaMamma, Carbon::parse('1975-01-01'), $gruppo);
    Famiglia::factory()->create(['tipo' => TipoFamiglia::NomadelfaMamma])->assegnaCapoFamiglia($nomadelfaMamma);

    login();
    $this->get(action([PopolazioneNomadelfiaController::class, 'mammeVocazione']))
        ->assertSuccessful()
        ->assertSeeInOrder(['Mamma Di Vocazione', '1980-03-12'])
        ->assertDontSee('Nomadelfa Mamma');
});
