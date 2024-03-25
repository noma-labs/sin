<?php

namespace Tests\Officina\Http;

use App\Admin\Models\User;
use App\Officina\Controllers\PrenotazioniController;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Spatie\Permission\Models\Role;

it('shows the booked veichiles', function () {
    login();
    $this->withoutExceptionHandling();
    $this->get(action([PrenotazioniController::class, 'prenotazioni']))
        ->assertSuccessful();
});

it('shows the search view of prenotazioni', function () {
    login();
    $this->withoutExceptionHandling();
    $this->get(action([PrenotazioniController::class, 'searchView']))
        ->assertSuccessful();
});

it('administrator_can_create_prenotazione', function () {
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
    $this->post(action([PrenotazioniController::class, 'prenotazioniSucc'], [
        'nome' => $p->id,
        'veicolo' => $v->id,
        'meccanico' => $m->id,
        'data_par' => $now->toDateString(),
        'ora_par' => '08:00',
        'data_arr' => $now->toDateString(),
        'ora_arr' => '11:00',
        'uso' => $u->ofus_iden,
        'destinazione' => 'my-destination',
    ]))->assertRedirect(route('officina.prenota'));

    $this->assertDatabaseHas('db_meccanica.prenotazioni', [
        'destinazione' => 'my-destination',
    ]);

    //        $this->get(action([PrenotazioniController::class, 'prenotazioni'], ['giorno' => 'oggi']))
    //            ->assertSuccessful();

});

it('other_users_cannot_create_prenotazioni', function () {
    $operator = User::create(['username' => 'biblio-operator', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $biblioAmm = Role::findByName('biblioteca-amm');
    $operator->assignRole($biblioAmm);

    login($operator);

    $this->get(route('officina.ricerca'))->assertForbidden();
});
