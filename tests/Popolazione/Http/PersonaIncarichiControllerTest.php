<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Incarico\Models\Incarico;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaIncaricoAction;
use Carbon\Carbon;

it('assigns an incarico to a persona from her profile and goes back to it', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $incarico = Incarico::factory()->create();

    $this->from(route('nomadelfia.person.profile', $persona->id))
        ->post(route('nomadelfia.persone.incarichi.assegna', $persona->id), [
            'incarico_id' => $incarico->id,
            'data_inizio' => '2024-03-01',
        ])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id))
        ->assertSessionHasNoErrors();

    expect($persona->incarichiAttuali()->sole()->pivot->toArray())
        ->toMatchArray(['incarico_id' => $incarico->id, 'data_inizio' => '2024-03-01']);
});

it('does not assign again an incarico the persona already has', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Gino']);
    $incarico = Incarico::factory()->create();
    app(AssegnaIncaricoAction::class)->execute($persona, $incarico, Carbon::parse('2020-01-01'));

    $this->post(route('nomadelfia.persone.incarichi.assegna', $persona->id), [
        'incarico_id' => $incarico->id,
        'data_inizio' => '2024-03-01',
    ])->assertSessionHasErrors(['incarico_id' => 'Gino ha già questo incarico.']);

    expect($persona->incarichi()->count())->toBe(1);
});

it('requires the incarico and the data di inizio', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(route('nomadelfia.persone.incarichi.assegna', $persona->id), [])
        ->assertSessionHasErrors([
            'incarico_id' => "L'incarico è obbligatorio.",
            'data_inizio' => "La data di inizio dell'incarico è obbligatoria.",
        ]);

    expect($persona->incarichi()->count())->toBe(0);
});

it('forbids to assign an incarico to a user without the permission to insert a person', function (): void {
    login(User::query()->create(['username' => 'senza-permessi-incarichi', 'email' => 'senza-permessi-incarichi@email.it', 'password' => 'secret', 'persona_id' => 0]));
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(route('nomadelfia.persone.incarichi.assegna', $persona->id), [
        'incarico_id' => Incarico::factory()->create()->id,
        'data_inizio' => '2024-03-01',
    ])->assertForbidden();

    expect($persona->incarichi()->count())->toBe(0);
});
