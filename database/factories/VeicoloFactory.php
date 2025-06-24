<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Officina\Models\Alimentazioni;
use App\Officina\Models\Impiego;
use App\Officina\Models\Modelli;
use App\Officina\Models\Tipologia;
use App\Officina\Models\Veicolo;
use Illuminate\Database\Eloquent\Factories\Factory;

final class VeicoloFactory extends Factory
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

    public function impiegoGrosseto()
    {
        return $this->state(function () {
            return [
                'impiego_id' => Impiego::where('nome', 'Grosseto')->first(),
            ];
        });
    }

    public function impiegoPersonale()
    {

        return $this->state(function () {
            return [
                'impiego_id' => Impiego::where('nome', 'personale')->first(),
            ];
        });
    }

    public function impiegoInterno()
    {

        return $this->state(function () {
            return [
                'impiego_id' => Impiego::where('nome', 'interno')->first(),
            ];
        });
    }

    public function impiegoViaggiLunghi()
    {

        return $this->state(function () {
            return [
                'impiego_id' => Impiego::where('nome', 'Viaggi Lunghi')->first(),
            ];
        });
    }

    public function impiegoRoma()
    {

        return $this->state(function () {
            return [
                'impiego_id' => Impiego::where('nome', 'roma')->first(),
            ];
        });
    }

    public function impiegoAutobus()
    {

        return $this->state(function () {
            return [
                'impiego_id' => Impiego::where('nome', 'Autobus')->first(),
            ];
        });
    }

    public function tipologiaAutovettura()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Autovettura')->first(),
            ];
        });
    }

    public function tipologiaAutocarro()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Autocarro')->first(),
            ];
        });
    }

    public function tipologiaAutobus()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Autobus')->first(),
            ];
        });
    }

    public function tipologiaCamper()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Camper')->first(),
            ];
        });
    }

    public function tipologiaCiclomotore()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Ciclomotore')->first(),
            ];
        });
    }

    public function tipologiaFurgoncino()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Furgoncino')->first(),
            ];
        });
    }

    public function tipologiaFurgone()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Furgone')->first(),
            ];
        });
    }

    public function tipologiaMezzoAgricolo()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Mezzo agricolo')->first(),
            ];
        });
    }

    public function tipologiaMotocarro()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Motocarro')->first(),
            ];
        });
    }

    public function tipologiaMotociclo()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Motociclo')->first(),
            ];
        });
    }

    public function tipologiaRimorchio()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Rimorchio')->first(),
            ];
        });
    }

    public function tipologiaTrattore()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Trattore')->first(),
            ];
        });
    }

    public function tipologiaVeicoloEdile()
    {
        return $this->state(function () {
            return [
                'tipologia_id' => Tipologia::where('nome', 'Veicolo edile')->first(),
            ];
        });
    }
}
