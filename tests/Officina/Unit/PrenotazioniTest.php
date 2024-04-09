<?php

namespace Tests\Officina\Unit;

use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    Prenotazioni::truncate();
});

it('show the bookings active today', function () {
    testTime()->freeze('2024-03-23 12:00:00');

    $startOfDay = Carbon::parse('2024-03-23')->startOfDay();
    $endOfDay = Carbon::parse('2024-03-23')->endOfDay();

    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(0);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-23 08:00'), Carbon::parse('2024-03-23 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(1);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 12:00'), Carbon::parse('2024-03-23 07:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(2);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-23 12:00'), Carbon::parse('2024-03-24 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(3);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 12:00'), Carbon::parse('2024-03-24 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(4);
    // excluded bookings
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 12:00'), Carbon::parse('2024-03-22 15:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(4);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-24 07:00'), Carbon::parse('2024-03-24 15:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(4);
});

it('show the bookings active yesterday', function () {
    testTime()->freeze('2024-03-23 12:00:00');

    $startOfDay = Carbon::yesterday()->startOfDay();
    $endOfDay = Carbon::yesterday()->endOfDay();

    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(0);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 08:00'), Carbon::parse('2024-03-22 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(1);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-21 08:00'), Carbon::parse('2024-03-22 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(2);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 08:00'), Carbon::parse('2024-03-23 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(3);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-21 08:00'), Carbon::parse('2024-03-24 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(4);
    // excluded bookings
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-21 08:00'), Carbon::parse('2024-03-21 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(4);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-23 08:00'), Carbon::parse('2024-03-23 12:00'))->create();
    expect(Veicolo::withBookingsIn($startOfDay, $endOfDay)->get()->whereNotNull('prenotazione_id')->count())->toBe(4);
});
