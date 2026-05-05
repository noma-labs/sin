<?php

declare(strict_types=1);

use App\Officina\Models\Prenotazioni;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(function (): void {
    Prenotazioni::truncate();
});

it('displays calendar page successfully', function () {
    login();

    $response = $this->get(route('officina.calendario'));
    $response->assertSuccessful();
    $response->assertViewIs('officina.reservations.calendar');
});

it('uses today date when no date parameter provided', function () {
    login();

    $response = $this->get(route('officina.calendario'));

    $today = Date::now()->toDateString();
    $viewDate = $response->viewData('date')->toDateString();

    expect($viewDate)->toBe($today);
});

it('filters calendar by date query parameter', function () {
    $filterDate = '2026-04-15';

    login();

    $response = $this->get(route('officina.calendario', ['date' => $filterDate]));

    $response->assertSuccessful();
    $viewDate = $response->viewData('date')->toDateString();

    expect($viewDate)->toBe($filterDate);
});

it('returns array structure for reservations by vehicle', function () {
    login();

    $response = $this->get(route('officina.calendario'));

    $reservationsByVehicle = $response->viewData('reservationsByVehicle');
    expect($reservationsByVehicle)->toBeArray();
});

it('passes current time to view', function () {
    login();

    $response = $this->get(route('officina.calendario'));

    $now = $response->viewData('now');
    expect($now)->not->toBeNull();
});

describe('calendar view filtering', function (): void {
    it('shows a prenotazione on the same day', function () {
        testTime()->freeze('2026-05-05 12:00:00');

        login();

        Prenotazioni::factory()
            ->prenotata(Carbon::parse('2026-05-05 10:00'), Carbon::parse('2026-05-05 14:00'))
            ->create();

        $response = $this->get(route('officina.calendario', ['date' => '2026-05-05']));

        $reservationColors = $response->viewData('reservationColors');
        expect($reservationColors)->toHaveCount(1);
    });

    it('shows a prenotazione starting today and ending tomorrow', function () {
        testTime()->freeze('2026-05-05 12:00:00');

        login();

        Prenotazioni::factory()
            ->prenotata(Carbon::parse('2026-05-05 14:00'), Carbon::parse('2026-05-06 10:00'))
            ->create();

        $response = $this->get(route('officina.calendario', ['date' => '2026-05-05']));

        $reservationColors = $response->viewData('reservationColors');
        expect($reservationColors)->toHaveCount(1);
    });

    it('does not show a prenotazione starting yesterday and ending tomorrow', function () {
        testTime()->freeze('2026-05-05 12:00:00');

        login();

        Prenotazioni::factory()
            ->prenotata(Carbon::parse('2026-05-04 10:00'), Carbon::parse('2026-05-06 14:00'))
            ->create();

        $response = $this->get(route('officina.calendario', ['date' => '2026-05-05']));

        $reservationColors = $response->viewData('reservationColors');
        expect($reservationColors)->toHaveCount(0);
    });

    it('shows a prenotazione starting yesterday and ending today', function () {
        testTime()->freeze('2026-05-05 12:00:00');

        login();

        Prenotazioni::factory()
            ->prenotata(Carbon::parse('2026-05-04 10:00'), Carbon::parse('2026-05-05 14:00'))
            ->create();

        $response = $this->get(route('officina.calendario', ['date' => '2026-05-05']));

        $reservationColors = $response->viewData('reservationColors');
        expect($reservationColors)->toHaveCount(1);
    });
});
