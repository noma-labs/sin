<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\PersonaPopolazioneController;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\JoinLeaveHistoryController;
use Carbon\Carbon;

it('can render the popolazione history of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([JoinLeaveHistoryController::class, 'index'], $persona->id))
        ->assertSuccessful();
});

it('shows all the cards of a person in Nomadelfia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::now(), GruppoFamiliare::all()->random());

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
        ->assertSuccessful()
        ->assertSee('Presente')
        ->assertSeeInOrder(['Dati anagrafici', 'Dati Nomadelfia', 'Dati Famiglia']);
});
it('shows a person who never entered Nomadelfia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
        ->assertSuccessful()
        ->assertSee('Mai entrato');
});

it('links the people of a gruppo familiare to their page', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::now(), $gruppo);

    $this->get(route('nomadelfia.gruppifamiliari.show', $gruppo->id))
        ->assertSuccessful()
        ->assertSee(route('nomadelfia.person.show', $persona->id));
});

it('shows the spouse of a capofamiglia and no family actions', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Moglie Da Mostrare']);
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($moglie);

    $this->get(action([PersonaPopolazioneController::class, 'show'], $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Coniuge:', 'Moglie Da Mostrare', 'Figli:'])
        ->assertDontSee('Nuova famiglia')
        ->assertDontSee('Aggiungi come figlio');
});

it('shows the parents of a figlio and offers a nuova famiglia when adult', function (): void {
    login();
    $padre = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Padre Da Mostrare']);
    $figlio = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($padre);
    $famiglia->assegnaFiglioNato($figlio);

    $this->get(action([PersonaPopolazioneController::class, 'show'], $figlio->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Genitori:', 'Padre Da Mostrare'])
        ->assertSee('Nuova famiglia')
        ->assertDontSee('Aggiungi come figlio');
});

it('offers both family actions to an adult without a family', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
        ->assertSuccessful()
        ->assertSee('Nessuna famiglia')
        ->assertSee('Nuova famiglia')
        ->assertSee('Aggiungi come figlio');
});

it('does not show a former wife as the spouse of a capofamiglia', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $exMoglie = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Ex Moglie Uscita']);
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($marito);
    $famiglia->assegnaMoglie($exMoglie);
    $famiglia->uscitaDalNucleoFamiliare($exMoglie);

    $this->get(action([PersonaPopolazioneController::class, 'show'], $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Coniuge:', 'Nessuno'])
        ->assertDontSee('Ex Moglie Uscita');
});

it('does not offer a nuova famiglia to a person who was already capofamiglia', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($persona);
    $famiglia->uscitaDalNucleoFamiliare($persona);

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
        ->assertSuccessful()
        ->assertDontSee('Nuova famiglia');
});

it('shows the last famiglia of a person outside every nucleo with the coniuge', function (): void {
    login();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $vedova = Persona::factory()->maggiorenne()->femmina()->create(['nominativo' => 'Vedova Da Mostrare']);
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Del Defunto']);
    $famiglia->assegnaCapoFamiglia($marito, stato: '0');
    $famiglia->assegnaMoglie($vedova);

    $this->get(action([PersonaPopolazioneController::class, 'show'], $marito->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Famiglia:', 'Famiglia Del Defunto', 'Posizione:', 'CAPO FAMIGLIA', 'Fuori da nucleo', 'Coniuge:', 'Vedova Da Mostrare'])
        ->assertDontSee('Nessuna famiglia');
});

it('shows the parents of a figlio outside the nucleo', function (): void {
    login();
    $padre = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Padre Da Mostrare']);
    $figlio = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($padre, stato: '0');
    $famiglia->assegnaFiglioNato($figlio, stato: '0');

    $this->get(action([PersonaPopolazioneController::class, 'show'], $figlio->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['FIGLIO NATO', 'Fuori da nucleo', 'Genitori:', 'Padre Da Mostrare']);
});

it('shows the famiglia as capofamiglia before the one as figlio for a person outside every nucleo', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Di Origine', 'data_creazione' => '1990-01-01'])
        ->assegnaFiglioNato($persona, stato: '0');
    Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Propria', 'data_creazione' => '1980-01-01'])
        ->assegnaCapoFamiglia($persona, stato: '0');

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
        ->assertSuccessful()
        ->assertSeeInOrder(['Famiglia:', 'Famiglia Propria', 'CAPO FAMIGLIA'])
        ->assertDontSee('Famiglia Di Origine');
});

it('shows the most recent famiglia of a person married more times and outside every nucleo', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->create(['nome_famiglia' => 'Primo Matrimonio', 'data_creazione' => '1980-01-01'])
        ->assegnaCapoFamiglia($persona, stato: '0');
    Famiglia::factory()->create(['nome_famiglia' => 'Secondo Matrimonio', 'data_creazione' => '1995-01-01'])
        ->assegnaCapoFamiglia($persona, stato: '0');

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
        ->assertSuccessful()
        ->assertSee('Secondo Matrimonio')
        ->assertDontSee('Primo Matrimonio');
});

it('does not show parents for a single', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    Famiglia::factory()->create()->componenti()->attach($persona->id, ['stato' => '1', 'posizione_famiglia' => 'SINGLE']);

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
        ->assertSuccessful()
        ->assertSee('SINGLE')
        ->assertDontSee('Genitori:');
});
