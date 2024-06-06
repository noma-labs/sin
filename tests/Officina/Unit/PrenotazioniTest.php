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

it('returns vehicle with  multiple booking in time range', function () {
    testTime()->freeze('2024-04-10 12:00:00');

    $veicolo = Veicolo::factory()->create();
    Prenotazioni::factory()
        ->veicolo($veicolo)
        ->prenotata(Carbon::parse('2024-04-10 08:00'), Carbon::parse('2024-04-10 12:00'))
        ->create();
    Prenotazioni::factory()
        ->veicolo($veicolo)
        ->prenotata(Carbon::parse('2024-04-10 09:00'), Carbon::parse('2024-04-10 15:00'))
        ->create();

    expect(Veicolo::withBookingsIn(Carbon::parse('2024-04-10 07:00'), Carbon::parse('2024-04-10 16:00'))->get()->whereNotNull('prenotazione_id')->count())
        ->toBe(2);
});

it('return all vechicles with a booking outside of the selected timerange', function () {
    testTime()->freeze('2024-04-10 12:00:00');

    $veicolo = Veicolo::factory()->create();

    $tot = Veicolo::count();

    Prenotazioni::factory()->veicolo($veicolo)->prenotata(Carbon::parse('2024-03-23 08:00'), Carbon::parse('2024-03-23 12:00'))->create();

    expect(Veicolo::withBookingsIn(Carbon::parse('2024-04-10 08:00'), Carbon::parse('2024-04-10 15:00'))->get())->toHaveCount($tot);

});

it('return correct bookings', function () {
    testTime()->freeze('2024-04-11 12:00:00');

    $veicolo = Veicolo::factory()->create();

    Prenotazioni::factory()
        ->veicolo($veicolo)
        ->prenotata(Carbon::parse('2024-04-11 08:00'), Carbon::parse('2024-04-11 09:00'))
        ->create();

    // start the day before
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-10 07:00'), Carbon::parse('2024-04-10 08:00'))->get())->toHaveCount(0);
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-10 07:00'), Carbon::parse('2024-04-11 07:00'))->get())->toHaveCount(0);
    // FIXME: expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-10 06:00'), Carbon::parse('2024-04-11 09:00'))->get())->toHaveCount(1);

    // start in the same day
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 06:00'), Carbon::parse('2024-04-11 07:00'))->get())->toHaveCount(0);
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 07:30'), Carbon::parse('2024-04-11 08:30'))->get())->toHaveCount(1);
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 08:00'), Carbon::parse('2024-04-11 09:00'))->get())->toHaveCount(1);
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 08:30'), Carbon::parse('2024-04-11 09:30'))->get())->toHaveCount(1);
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 10:00'), Carbon::parse('2024-04-11 11:00'))->get())->toHaveCount(0);
    // FIXME: expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 08:00'), Carbon::parse('2024-04-12 08:00'))->get())->toHaveCount(1);

    // start day after
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-12 06:00'), Carbon::parse('2024-04-12 07:00'))->get())->toHaveCount(0);

    // span the day
    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-10 08:00'), Carbon::parse('2024-04-12 08:00'))->get())->toHaveCount(1);
});

it('allow booking a vehicle with a deleted booking', function () {
    testTime()->freeze('2024-04-11 12:00:00');

    $veicolo = Veicolo::factory()->create();

    $booking = Prenotazioni::factory()
        ->veicolo($veicolo)
        ->prenotata(Carbon::parse('2024-04-11 08:00'), Carbon::parse('2024-04-11 09:00'))
        ->create();

    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 07:00'), Carbon::parse('2024-04-11 09:00'))->get())->toHaveCount(1);

    $booking->delete();

    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 07:00'), Carbon::parse('2024-04-11 09:00'))->get())->toHaveCount(0);
});


it('allow booking a vehicle with a deleted booking spanning multiple days', function () {
    testTime()->freeze('2024-04-11 12:00:00');

    $veicolo = Veicolo::factory()->create();

    $booking = Prenotazioni::factory()
        ->veicolo($veicolo)
        ->prenotata(Carbon::parse('2024-04-10 08:00'), Carbon::parse('2024-04-20 09:00'))
        ->create();

    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 07:00'), Carbon::parse('2024-04-11 09:00'))->get())->toHaveCount(1);

    $booking->delete();

    expect(Prenotazioni::inTimeRange(Carbon::parse('2024-04-11 07:00'), Carbon::parse('2024-04-11 09:00'))->get())->toHaveCount(0);
});
