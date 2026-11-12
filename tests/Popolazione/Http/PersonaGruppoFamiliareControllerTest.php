<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Controllers\MovePersonGruppoFamiliareController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonGruppoFamiliareController;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Carbon\Carbon;

it('can render the gruppofamiliare index page', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonGruppoFamiliareController::class, 'index'], $persona->id))
        ->assertSuccessful();
});

it('can update the date of a gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();

    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);

    $new_data_entrata = Carbon::now()->addDay()->toDatestring();

    $this->put(action([PersonGruppoFamiliareController::class, 'update'], ['id' => $persona->id, 'idGruppo' => $gruppo->id]),
        [
            'current_data_entrata' => $data_entrata,
            'new_data_entrata' => $new_data_entrata,
        ])
        ->assertRedirect(route('nomadelfia.person.gruppo', $persona->id));
});

it('can assign a persona to a gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);

    $new_data_entrata = Carbon::now()->addDay()->toDatestring();

    $this->post(action([PersonGruppoFamiliareController::class, 'store'], $persona->id),
        [
            'gruppo_id' => GruppoFamiliare::all()->random()->id,
            'data_entrata' => $new_data_entrata,
        ])
        ->assertRedirect(route('nomadelfia.person.gruppo', $persona->id));
});

it('can delete a persona from a gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);

    $this->delete(action([PersonGruppoFamiliareController::class, 'delete'], ['id' => $persona->id,  'idGruppo' => $gruppo->id]))
        ->assertRedirect(route('nomadelfia.person.gruppo', $persona->id));
});

it('can move a persona from another gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);
    $new_data_entrata = Carbon::now()->addDay()->toDatestring();

    $this->post(action([MovePersonGruppoFamiliareController::class, 'store'], ['id' => $persona->id,  'idGruppo' => $gruppo->id]),
        [
            'new_gruppo_id' => GruppoFamiliare::all()->random()->id,
            'new_data_entrata' => $new_data_entrata,
            'current_data_entrata' => $data_entrata,
        ]
    )
        ->assertRedirect(route('nomadelfia.person.gruppo', $persona->id));
});

it('puts a persona in a gruppo familiare from the profile and goes back to it', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();

    $this->from(route('nomadelfia.person.profile', $persona->id))
        ->post(route('nomadelfia.person.gruppo.store', $persona->id), [
            'gruppo_id' => $gruppo->id,
            'data_entrata' => '2024-02-01',
            'torna_indietro' => '1',
        ])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id));

    expect($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id);
});

it('moves a persona to another gruppo familiare from the profile and goes back to it', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    [$gruppo, $nuovo] = GruppoFamiliare::query()->take(2)->get()->all();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2020-01-10'), $gruppo);

    $this->from(route('nomadelfia.person.profile', $persona->id))
        ->post(route('nomadelfia.persone.gruppo.sposta', ['id' => $persona->id, 'idGruppo' => $gruppo->id]), [
            'new_gruppo_id' => $nuovo->id,
            'new_data_entrata' => '2024-02-01',
            'current_data_entrata' => '2020-01-10',
            'torna_indietro' => '1',
        ])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id));

    expect($persona->gruppofamiliareAttuale()->id)->toBe($nuovo->id)
        ->and($persona->gruppofamiliariStorico()->sole()->pivot->data_uscita_gruppo)->toBe('2024-02-01');
});
