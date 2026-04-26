<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Date;

it('displays calendar page successfully', function () {
    $response = $this->get(route('officina.calendario'));

    $response->assertSuccessful();
    $response->assertViewIs('officina.reservations.calendar');
});

it('uses today date when no date parameter provided', function () {
    $response = $this->get(route('officina.calendario'));

    $today = Date::now()->toDateString();
    $viewDate = $response->viewData('date')->toDateString();

    expect($viewDate)->toBe($today);
});

it('filters calendar by date query parameter', function () {
    $filterDate = '2026-04-15';

    $response = $this->get(route('officina.calendario', ['date' => $filterDate]));

    $response->assertSuccessful();
    $viewDate = $response->viewData('date')->toDateString();

    expect($viewDate)->toBe($filterDate);
});

it('returns array structure for reservations by vehicle', function () {
    $response = $this->get(route('officina.calendario'));

    $reservationsByVehicle = $response->viewData('reservationsByVehicle');
    expect($reservationsByVehicle)->toBeArray();
});

it('passes current time to view', function () {
    $response = $this->get(route('officina.calendario'));

    $now = $response->viewData('now');
    expect($now)->not->toBeNull();
});
