<?php

namespace Tests\Officina\Unit;

use App\Officina\Models\Prenotazioni;
use Carbon\Carbon;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    Prenotazioni::truncate();
});

it('show the bookings active today', function () {
    testTime()->freeze('2024-03-23 12:00:00');

    expect(Prenotazioni::today()->count())->toBe(0);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-23 08:00'), Carbon::parse('2024-03-23 12:00'))->create();
    expect(Prenotazioni::today()->count())->toBe(1);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 12:00'), Carbon::parse('2024-03-23 07:00'))->create();
    expect(Prenotazioni::today()->count())->toBe(2);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-23 12:00'), Carbon::parse('2024-03-24 12:00'))->create();
    expect(Prenotazioni::today()->count())->toBe(3);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 12:00'), Carbon::parse('2024-03-24 12:00'))->create();
    expect(Prenotazioni::today()->count())->toBe(4);
    // excluded bookings
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 12:00'), Carbon::parse('2024-03-22 15:00'))->create();
    expect(Prenotazioni::today()->count())->toBe(4);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-24 07:00'), Carbon::parse('2024-03-24 15:00'))->create();
    expect(Prenotazioni::today()->count())->toBe(4);
})->only();

it('show the bookings active yesterday', function () {
    testTime()->freeze('2024-03-23 12:00:00');

    expect(Prenotazioni::yesterday()->count())->toBe(0);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 08:00'), Carbon::parse('2024-03-22 12:00'))->create();
    expect(Prenotazioni::yesterday()->count())->toBe(1);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-21 08:00'), Carbon::parse('2024-03-22 12:00'))->create();
    expect(Prenotazioni::yesterday()->count())->toBe(2);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 08:00'), Carbon::parse('2024-03-23 12:00'))->create();
    expect(Prenotazioni::yesterday()->count())->toBe(3);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-21 08:00'), Carbon::parse('2024-03-24 12:00'))->create();
    expect(Prenotazioni::yesterday()->count())->toBe(4);
    // excluded bookings
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-21 08:00'), Carbon::parse('2024-03-21 12:00'))->create();
    expect(Prenotazioni::yesterday()->count())->toBe(4);
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-23 08:00'), Carbon::parse('2024-03-23 12:00'))->create();
    expect(Prenotazioni::yesterday()->count())->toBe(4);
})->only();
