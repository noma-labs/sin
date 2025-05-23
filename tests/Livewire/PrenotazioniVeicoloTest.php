<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Livewire\PrenotazioneVeicoli;
use App\Nomadelfia\Persona\Models\Persona;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Livewire\Livewire;

use function Spatie\PestPluginTestTime\testTime;

it('can render succesfully the component', function (): void {
    Livewire::test(PrenotazioneVeicoli::class)->assertStatus(200);
});

it('refresh veicoli', function (): void {
    Prenotazioni::truncate();
    testTime()->freeze('2024-04-09 12:00:00');

    Prenotazioni::factory()
        ->prenotata(Carbon::parse('2024-04-09 08:00'), Carbon::parse('2024-04-09 15:00'))
        ->veicolo(Veicolo::factory()->impiegoPersonale()->tipologiaMacchina()->create())
        ->cliente(Persona::factory()->create())
        ->create();

    Livewire::test(PrenotazioneVeicoli::class)
        ->set('dataPartenza', '2024-04-09')
        ->set('dataArrivo', '2024-04-09')
        ->set('oraPartenza', '07:00')
        ->set('oraArrivo', '16:00')
        ->call('refreshVeicoli')
        ->assertSee('2024-04-09:08:00')
        ->assertSee('2024-04-09:15:00')
        ->assertDontSee('--orari di partenza e arrivo non validi--')
        ->assertSee('--seleziona veicolo--');
});
