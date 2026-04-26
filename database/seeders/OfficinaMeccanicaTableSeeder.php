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

        $impieghiTipologie = [
            'impiegoGrosseto' => [
                'tipologiaAutovettura',
                'tipologiaFurgoncino',
                'tipologiaFurgone',
                'tipologiaMotociclo',
            ],
            'impiegoPersonale' => [
                'tipologiaAutovettura',
                'tipologiaFurgoncino',
                'tipologiaFurgone',
            ],
            'impiegoInterno' => [
                'tipologiaAutovettura',
                'tipologiaFurgoncino',
                'tipologiaFurgone',
            ],
            'impiegoViaggiLunghi' => [
                'tipologiaAutovettura',
                'tipologiaFurgoncino',
                'tipologiaFurgone',
                'tipologiaAutobus',
                'tipologiaAutocarro',
            ],
            'impiegoRoma' => [
                'tipologiaAutovettura',
                'tipologiaFurgoncino',
                'tipologiaFurgone',
                'tipologiaAutocarro',
            ],
        ];

        foreach ($impieghiTipologie as $impiego => $tipologie) {
            foreach ($tipologie as $tipologia) {
                $randomCount = rand(1, 5);
                Veicolo::factory($randomCount)
                    ->{$impiego}()
                    ->{$tipologia}()
                    ->create();
            }
        }

        $impiegoGrossetoId = Impiego::where('nome', 'Grosseto')->first()->id;
        $veicoloGrosseto = Veicolo::where('impiego_id', $impiegoGrossetoId)->first();
        $veicoloGrosseto2 = Veicolo::where('impiego_id', $impiegoGrossetoId)->skip(1)->first();
        $veicoloGrosseto3 = Veicolo::where('impiego_id', $impiegoGrossetoId)->skip(3)->first();

        Prenotazioni::factory()
            ->prenotata(Carbon::now(), Carbon::now()->addHours(2))
            ->veicolo($veicoloGrosseto)
            ->create();

        Prenotazioni::factory()
            ->prenotata(Carbon::now(), Carbon::now()->addHours(2))
            ->veicolo($veicoloGrosseto2)
            ->create();

       Prenotazioni::factory()
            ->prenotata(Carbon::now()->addHours(2),Carbon::now()->addHours(4))
            ->veicolo($veicoloGrosseto2)
            ->create();

        Prenotazioni::factory()
            ->prenotata(Carbon::now()->addHours(3), Carbon::now()->addHours(5))
            ->veicolo($veicoloGrosseto3)
            ->create();

        // Multi-day reservation spanning yesterday to tomorrow
        Prenotazioni::factory()
            ->prenotata(Carbon::now()->subDay()->setHour(14), Carbon::now()->addDay()->setHour(10))
            ->veicolo($veicoloGrosseto)
            ->create();

    }
}