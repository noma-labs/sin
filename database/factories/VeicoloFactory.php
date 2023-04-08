<?php

namespace Database\Factories;

use App\Officina\Models\Alimentazioni;
use App\Officina\Models\Impiego;
use App\Officina\Models\Modelli;
use App\Officina\Models\Tipologia;
use App\Officina\Models\Veicolo;
use Illuminate\Database\Eloquent\Factories\Factory;

class VeicoloFactory extends Factory
{
    protected $model = Veicolo::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->name,
            'targa' => $this->faker->name(5),
            'impiego_id' => Impiego::all()->random(),
            'prenotabile' => 1,
            'modello_id' => Modelli::all()->random(),
            'tipologia_id' => Tipologia::all()->random(),
            'alimentazione_id' => Alimentazioni::all()->random(),
        ];
    }
}
