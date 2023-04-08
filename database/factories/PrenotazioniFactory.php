<?php

namespace Database\Factories;

use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrenotazioniFactory extends Factory
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

    public function partita(Carbon $data_partenza, string $ora_partenza = '08:00')
    {
        return $this->state(function (array $attributes) use ($ora_partenza, $data_partenza) {
            return [
                'data_partenza' => $data_partenza->toDateString(),
                'ora_partenza' => $ora_partenza,
            ];
        });
    }

    public function rientraInGiornata(string $ora_partenza = '08:00', string $ora_arrivo = '12:00')
    {
        return $this->state(function (array $attributes) use ($ora_partenza, $ora_arrivo) {
            return [
                'data_partenza' => Carbon::now()->toDateString(),
                'ora_partenza' => $ora_partenza,
                'data_arrivo' => Carbon::now()->toDateString(),
                'ora_arrivo' => $ora_arrivo,
            ];
        });
    }

    public function partitaIeri(string $ora_partenza = '08:00')
    {
        return $this->state(function (array $attributes) use ($ora_partenza) {
            return [
                'data_partenza' => Carbon::now()->subDay()->toDateString(),
                'ora_partenza' => $ora_partenza,
            ];
        });
    }

    public function ritornaIeri(string $ora_arrivo = '12:00')
    {
        return $this->state(function (array $attributes) use ($ora_arrivo) {
            return [
                'data_arrivo' => Carbon::now()->subDay()->toDateString(),
                'ora_arrivo' => $ora_arrivo,
            ];
        });
    }

    public function ritornaDomani(string $ora_arrivo = '12:00')
    {
        return $this->state(function (array $attributes) use ($ora_arrivo) {
            return [
                'data_arrivo' => Carbon::now()->addDay()->toDateString(),
                'ora_arrivo' => $ora_arrivo,
            ];
        });
    }

    public function ritornaOggi(string $ora_arrivo = '12:00')
    {
        return $this->state(function (array $attributes) use ($ora_arrivo) {
            return [
                'data_arrivo' => Carbon::now()->toDateString(),
                'ora_arrivo' => $ora_arrivo,
            ];
        });
    }
}
