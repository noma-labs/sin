<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Officina\Models\Impiego;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Veicolo;
use App\Patente\Models\Patente;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class PatenteTableSeeder extends Seeder
{
    public function run()
    {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::now(), GruppoFamiliare::all()->random());
        Patente::factory()->persona($persona)->create();
    }
}
