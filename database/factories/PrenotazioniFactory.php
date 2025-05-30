<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Nomadelfia\Persona\Models\Persona;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PrenotazioniFactory extends Factory
{
    protected $model = Prenotazioni::class;

    public function definition()
    {
        return [
            'cliente_id' => Persona::factory(),
            'veicolo_id' => Veicolo::factory(),
            'meccanico_id' => Persona::factory(),
            'uso_id' => Uso::all()->first(),
            'note' => $this->faker->text,
            'destinazione' => $this->faker->title,
        ];
    }

    public function cliente(Persona $persona)
    {
        return $this->state(function () use ($persona) {
            return [
                'cliente_id' => $persona,
            ];
        });
    }

    public function veicolo(Veicolo $veicolo)
    {
        return $this->state(function () use ($veicolo) {
            return [
                'veicolo_id' => $veicolo,
            ];
        });
    }

    public function prenotata(Carbon $partenza, Carbon $arrivo)
    {
        return $this->state(function () use ($partenza, $arrivo) {
            return [
                'data_partenza' => $partenza->toDateString(),
                'ora_partenza' => $partenza->format('H:i'),
                'data_arrivo' => $arrivo->toDateString(),
                'ora_arrivo' => $arrivo->format('H:i'),
            ];
        });
    }
}
