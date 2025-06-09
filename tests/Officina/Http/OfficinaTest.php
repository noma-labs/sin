<?php

declare(strict_types=1);

namespace Tests\Officina\Http;

use App\Admin\Models\User;
use App\Livewire\PrenotazioneVeicoli;
use App\Nomadelfia\Persona\Models\Persona;
use App\Officina\Controllers\PrenotazioniController;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

it('forbids access to guests', function (): void {
    $this
        ->get(action([PrenotazioniController::class, 'index']))
        ->assertRedirect(route('login'));
});

it('shows the booked vehicles', function (): void {
    login();
    $this->withoutExceptionHandling();
    $this->get(action([PrenotazioniController::class, 'index']))
        ->assertSuccessful();
});

it('shows the search view of prenotazioni', function (): void {
    login();
    $this->withoutExceptionHandling();
    $this->get(action([PrenotazioniController::class, 'index']))
        ->assertSuccessful();
});

it('administrator can create prenotazione', function (): void {
    $v = Veicolo::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $p = Persona::find($persona->id);
    $m = Persona::factory()->maggiorenne()->maschio()->create();
    $u = Uso::all()->random();
    $this->assertNotEmpty($u);

    $meccanicaAmm = User::create(['username' => 'meccanica-admin', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $meccanicaAmmRole = Role::findByName('meccanica-amm');
    $meccanicaAmm->assignRole($meccanicaAmmRole);

    login($meccanicaAmm);

    $now = Carbon::now();
    $this->post(action([PrenotazioniController::class, 'index'], [
        'nome' => $p->id,
        'veicolo' => $v->id,
        'meccanico' => $m->id,
        'data_par' => $now->toDateString(),
        'ora_par' => '08:00',
        'data_arr' => $now->toDateString(),
        'ora_arr' => '11:00',
        'uso' => $u->ofus_iden,
        'destinazione' => 'my-destination',
    ]));

    $this->assertDatabaseHas('db_meccanica.prenotazioni', [
        'destinazione' => 'my-destination',
    ]);

});

it('other_users_cannot_create_prenotazioni', function (): void {
    $operator = User::create(['username' => 'biblio-operator', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $biblioAmm = Role::findByName('biblioteca-amm');
    $operator->assignRole($biblioAmm);

    login($operator);

    $this->get(route('officina.ricerca'))->assertForbidden();
});

it('render the livewire <prenotazioni-veicoli> on the edit page', function (): void {
    $prenotazione = Prenotazioni::factory()
        ->prenotata(Carbon::parse('2024-04-10 08:00'), Carbon::parse('2024-04-10 12:00'))
        ->create();

    login();

    $this->get(route('officina.prenota.modifica', ['id' => $prenotazione->id]))
        ->assertSeeLivewire(PrenotazioneVeicoli::class);
});
