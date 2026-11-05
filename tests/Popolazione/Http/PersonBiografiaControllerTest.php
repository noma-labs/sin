<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;

it('updates only the biografia of a person and goes back to the profile', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['biografia' => 'Vecchia biografia']);

    $this->put(route('anagrafe.person.biografia.update', $persona->id), ['biografia' => 'Nuova biografia'])
        ->assertRedirectToRoute('nomadelfia.person.profile', $persona->id);

    expect($persona->fresh())
        ->biografia->toBe('Nuova biografia')
        ->nome->toBe($persona->nome);
});

it('clears the biografia of a person when it is empty', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['biografia' => 'Da cancellare']);

    $this->put(route('anagrafe.person.biografia.update', $persona->id), ['biografia' => ''])
        ->assertRedirectToRoute('nomadelfia.person.profile', $persona->id);

    expect($persona->fresh()->biografia)->toBeNull();
});

it('forbids to update the biografia to a user without the permission to modify a person', function (): void {
    $user = User::query()->create(['username' => 'senza-permessi-bio', 'email' => 'senza-permessi-bio@email.it', 'password' => 'secret', 'persona_id' => 0]);
    login($user);
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['biografia' => 'Da tenere']);

    $this->put(route('anagrafe.person.biografia.update', $persona->id), ['biografia' => 'Nuova'])
        ->assertForbidden();

    expect($persona->fresh()->biografia)->toBe('Da tenere');
});
