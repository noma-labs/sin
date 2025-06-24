<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\Persona\Models\Persona;
use App\Officina\Models\Impiego;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class OfficinaMeccanicaTableSeeder extends Seeder
{
    public function run()
    {
        $meccanica = Azienda::where('nome_azienda', 'like', 'Officina%')->first();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();

        $persona->aziende()->attach($meccanica, [
            'stato' => 'Attivo',
            'data_inizio_azienda' => Carbon::now()->toDateString(),
            'mansione' => Azienda::MANSIONE_RESPONSABILE,
        ]);

        // Array dei metodi di tipologia
        $tipologie = [
            'tipologiaAutovettura',
            'tipologiaAutocarro',
            'tipologiaAutobus',
            'tipologiaCamper',
            'tipologiaCiclomotore',
            'tipologiaFurgoncino',
            'tipologiaFurgone',
            'tipologiaMezzoAgricolo',
            'tipologiaMotocarro',
            'tipologiaMotociclo',
            'tipologiaRimorchio',
            'tipologiaTrattore',
            'tipologiaVeicoloEdile',
        ];

        // Array dei metodi di impiego
        $impieghi = [
            'impiegoGrosseto',
            'impiegoPersonale',
            'impiegoInterno',
            'impiegoViaggiLunghi',
            'impiegoRoma',
            'impiegoAutobus',
        ];

        // Crea un veicolo per ogni combinazione tipologia/impiego
        foreach ($tipologie as $tipologia) {
            foreach ($impieghi as $impiego) {
                $randomCount = rand(1, 5); // Numero casuale di veicoli da creare per ogni combinazione
                Veicolo::factory($randomCount)
                    ->{$impiego}()
                    ->{$tipologia}()
                    ->create();
            }
        }

        $impiegoGrossetoId = Impiego::where('nome', 'Grosseto')->first()->id;
        $veicoloGrosseto = Veicolo::where('impiego_id', $impiegoGrossetoId)->first();
        $veicoloGrosseto2 = Veicolo::where('impiego_id', $impiegoGrossetoId)->skip(1)->first();

        Prenotazioni::factory()
            ->prenotata(Carbon::now(), Carbon::now()->addHour(2))
            ->veicolo($veicoloGrosseto)
            ->create();

        Prenotazioni::factory()
            ->prenotata(Carbon::now(), Carbon::now()->addHour(2))
            ->veicolo($veicoloGrosseto2)
            ->create();
    }
}
