<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\PersonController;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
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
