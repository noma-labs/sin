<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Patente\Models\Patente;
use App\Patente\Models\CategoriaPatente;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class PatenteTableSeeder extends Seeder
{
    public function run()
    {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::now(), GruppoFamiliare::all()->random());

        $patente = Patente::factory()->persona($persona)->create();
        $patente->refresh();

        $categoria = CategoriaPatente::inRandomOrder()->first();
        $patente->categorie()->attach($categoria->id);
    }
}
