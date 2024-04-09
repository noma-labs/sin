<?php

namespace Tests\Livewire;

use App\Livewire\PrenotazioneVeicoli;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Livewire\Livewire;

use function Spatie\PestPluginTestTime\testTime;

it('can render succesfully the component', function () {
    Livewire::test(PrenotazioneVeicoli::class)->assertStatus(200);
});

it('refresh veicoli', function () {
    Prenotazioni::truncate();
    testTime()->freeze('2024-04-09 12:00:00');

    $veicolo = Veicolo::factory()->impiegoPersonale()->tipologiaMacchina()->create();
    $persona = Persona::factory()->create();
    Prenotazioni::factory()
        ->prenotata(Carbon::parse('2024-04-09 08:00'), Carbon::parse('2024-04-09 15:00'))
        ->veicolo($veicolo)
        ->cliente($persona)
        ->create();

    Livewire::test(PrenotazioneVeicoli::class)
        ->set('dataPartenza', '2024-04-09')
        ->set('dataArrivo', '2024-04-09')
        ->set('oraPartenza', '07:00')
        ->set('oraArrivo', '16:00')
        ->call('refreshVeicoli')
        ->assertSee($persona->nominativo)
        ->assertSee($veicolo->id);
});
