<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\Prenotazioni;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

final class ReservationCalendarController
{
    public function __invoke(Request $request): View
    {
        $now = Date::now();
        $date = $request->filled('date')
            ? Date::parse($request->string('date')->toString())->startOfDay()
            : $now;

        $reservations = Prenotazioni::query()
            ->where('data_partenza', '=', $date->toDateString())
            ->orWhere('data_arrivo', '=', $date->toDateString())
            ->with('veicolo', 'cliente')
            ->orderBy('data_partenza', 'asc')
            ->get();

        $vehicles = $reservations->pluck('veicolo')->unique('id');

        $reservationsByVehicle = [];
        foreach ($vehicles as $vehicle) {
            $reservationsByVehicle[$vehicle->id] = array_fill(0, 24, []); // Hours 0-23
        }

        foreach ($reservations as $pren) {
            $startHour = Date::parse($pren->ora_partenza)->hour;
            $reservationsByVehicle[$pren->veicolo_id][$startHour][] = $pren;
        }

        $hexColors = [
            '#5b8dee', // muted blue
            '#3da37a', // muted green
            '#e06b74', // muted red
            '#c9972b', // muted amber
            '#3aabbd', // muted teal
            '#8b7fb8', // muted purple
            '#c97b3a', // muted orange
            '#b55e8e', // muted pink
            '#4e9a6f', // forest green
            '#d46a3a', // burnt sienna
            '#5c7fc4', // slate blue
            '#a05c9e', // mauve
            '#2e8f8f', // dark cyan
            '#c45c5c', // dusty rose
            '#7d9c3a', // olive green
            '#e08c3a', // warm orange
            '#5a7abf', // periwinkle
            '#3d8c7a', // sea green
            '#b07a30', // golden brown
            '#9c5c7a', // plum
        ];

        $reservationColors = [];
        foreach ($reservations as $index => $pren) {
            $reservationColors[$pren->id] = $hexColors[$index % count($hexColors)];
        }

        return view('officina.reservations.calendar', compact('vehicles', 'reservationsByVehicle', 'reservationColors', 'now', 'date'));
    }
}
