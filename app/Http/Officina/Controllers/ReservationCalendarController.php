<?php

declare(strict_types=1);

namespace App\Http\Officina\Controllers;

use App\Officina\Models\Prenotazioni;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

final class ReservationCalendarController
{
    public function __invoke(Request $request): View
    {
        $now = Date::now();
        $prenotazioni = Prenotazioni::query()
            ->where('data_partenza', '=', $now->toDateString())
            ->orWhere('data_arrivo', '=', $now->toDateString())
            ->with('veicolo', 'cliente')
            ->orderBy('data_partenza', 'asc')
            ->get();

        $vehicles = $prenotazioni->pluck('veicolo')->unique('id');

        // Organize reservations by vehicle ID and hour
        $reservations = [];
        foreach ($vehicles as $vehicle) {
            $reservations[$vehicle->id] = array_fill(6, 16, []); // Hours 6-22
        }

        foreach ($prenotazioni as $pren) {
            $startHour = (int) substr($pren->ora_partenza, 0, 2);
            $endHour = (int) substr($pren->ora_arrivo, 0, 2);

            // For reservations that span multiple hours, place in the hour it starts
            for ($hour = $startHour; $hour < $endHour && $hour < 22; $hour++) {
                $reservations[$pren->veicolo_id][$hour][] = $pren;
            }
        }

        // Create color mapping for clients
        $colors = ['primary', 'success', 'danger', 'warning', 'info', 'secondary'];
        $clientColors = [];
        foreach ($prenotazioni as $pren) {
            if (! isset($clientColors[$pren->cliente_id])) {
                $clientColors[$pren->cliente_id] = $colors[count($clientColors) % count($colors)];
            }
        }

        return view('officina.reservations.calendar', compact('vehicles', 'reservations', 'clientColors'));
    }
}
