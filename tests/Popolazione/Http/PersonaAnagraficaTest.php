<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Controllers\PersonController;
use App\Anagrafe\Persona\Controllers\PersonIdentityController;
use App\Anagrafe\Persona\Models\Persona;

it('shows form to create persona', function (): void {
    login();
    $this->get(action([PersonController::class, 'create']))
        ->assertSuccessful();
});

it('shows form to edit anagrafica', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    login();
    $this->get(action([PersonIdentityController::class, 'edit'], $persona->id))
        ->assertSuccessful();
});

it('can update anagrafica without touching the biografia', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['biografia' => 'Biografia da tenere']);

    login();

    $newName = 'My-name';
    $newSurname = 'My-surnema';
    $newNascita = '2022-12-12';
    $newLuogo = 'my-luogo';
    $newSesso = 'F';
    $this->put(action([PersonIdentityController::class, 'update'], $persona->id),
        [
            'nome' => $newName,
            'cognome' => $newSurname,
            'datanascita' => $newNascita,
            'luogonascita' => $newLuogo,
            'sesso' => $newSesso,
        ])
        ->assertRedirect()
        ->assertRedirectContains(route('nomadelfia.person.profile', $persona->id));

    $p = Persona::findOrFail($persona->id);
    $this->assertEquals($newSurname, $p->cognome);
    $this->assertEquals($newSesso, $p->sesso);
    $this->assertEquals($newLuogo, $p->provincia_nascita);
    $this->assertEquals($newName, $p->nome);
    $this->assertEquals($newNascita, $p->data_nascita);
    $this->assertEquals('Biografia da tenere', $p->biografia);
});

it('can insert a persona', function (): void {
    login();
    $this->withoutExceptionHandling();
    $this->post(action([PersonController::class, 'store']),
        [
            'nominativo' => 'my-name',
            'nome' => 'name',
            'cognome' => 'my-surname',
            'data_nascita' => '2022-10-10',
            'luogo_nascita' => 'Grosseto',
            'sesso' => 'M',
        ])
        ->assertRedirectToRoute('nomadelfia.person.profile', Persona::where('nominativo', 'my-name')->latest('id')->firstOrFail()->id);
});
