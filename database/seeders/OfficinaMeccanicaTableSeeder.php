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
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class OfficinaMeccanicaTableSeeder extends Seeder
{
    public function run()
    {
        $meccanica = Azienda::where('nome_azienda', 'like', 'Officina%')->first();

        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $act = app(EntrataMaggiorenneSingleAction::class);
        $act->execute($persona, Carbon::now(), GruppoFamiliare::all()->random());

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
                $randomCount = rand(1, 15);
                Veicolo::factory($randomCount)
                    ->{$impiego}()
                    ->{$tipologia}()
                    ->create();
            }
        }

        $impiegoGrossetoId = Impiego::where('nome', 'Grosseto')->first()->id;
        $veicoliGrosseto = Veicolo::where('impiego_id', $impiegoGrossetoId)->get();

        if ($veicoliGrosseto->isEmpty()) {
            return;
        }

        $base = Carbon::now()->startOfDay()->setHour(7);

        $timeSlots = [
            [$base->copy()->addMinutes(0), $base->copy()->addMinutes(60)],
            [$base->copy()->addMinutes(30), $base->copy()->addMinutes(90)],
            [$base->copy()->addMinutes(60), $base->copy()->addMinutes(120)],
            [$base->copy()->addMinutes(90), $base->copy()->addMinutes(150)],
            [$base->copy()->addMinutes(120), $base->copy()->addMinutes(180)],
            [$base->copy()->addMinutes(150), $base->copy()->addMinutes(210)],
            [$base->copy()->addMinutes(180), $base->copy()->addMinutes(240)],
            [$base->copy()->addMinutes(210), $base->copy()->addMinutes(270)],
            [$base->copy()->addMinutes(240), $base->copy()->addMinutes(300)],
            [$base->copy()->addMinutes(270), $base->copy()->addMinutes(330)],
            [$base->copy()->addMinutes(300), $base->copy()->addMinutes(360)],
            [$base->copy()->addMinutes(330), $base->copy()->addMinutes(390)],
            [$base->copy()->addMinutes(360), $base->copy()->addMinutes(420)],
            [$base->copy()->addMinutes(390), $base->copy()->addMinutes(450)],
            [$base->copy()->addMinutes(420), $base->copy()->addMinutes(480)],
            [$base->copy()->addMinutes(450), $base->copy()->addMinutes(510)],
            [$base->copy()->addMinutes(480), $base->copy()->addMinutes(540)],
            [$base->copy()->addMinutes(510), $base->copy()->addMinutes(570)],
            [$base->copy()->addMinutes(540), $base->copy()->addMinutes(600)],
            [$base->copy()->addMinutes(570), $base->copy()->addMinutes(630)],
        ];

        foreach ($timeSlots as $index => [$partenza, $arrivo]) {
            $veicolo = $veicoliGrosseto[$index % $veicoliGrosseto->count()];

            Prenotazioni::factory()
                ->prenotata($partenza, $arrivo)
                ->veicolo($veicolo)
                ->create();
        }

    }
}
