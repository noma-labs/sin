<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ManutenzioneProgrammataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'VERIFICA OLIO MOTORE',
            'ore' => 10,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'VERIFICA ACQUA RADIATORE',
            'ore' => 10,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'PULIZIA FILTRO ARIA',
            'ore' => 10,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'PULIZIA RADIATORE',
            'ore' => 10,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'PULIZIA FILTRO ARIA CABINA',
            'ore' => 100,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'INGRASSATURA GENERALE',
            'ore' => 100,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'CONTROLLO PRESSIONE PNEUMATICI',
            'ore' => 100,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'CONTROLLO LIVELLO OLIO FRENI',
            'ore' => 100,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'CONTROLLO OLIO PONTE ANT E POST',
            'ore' => 500,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'SOSTITUZIONE FILTRI GASOLIO',
            'ore' => 500,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'SOSTITUZIONE FILTRO SERVIZI',
            'ore' => 1000,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'SOSTITUZIONE FILTRO OLIO',
            'ore' => 1000,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'SOSTITUZIONE FILTRO ARIA',
            'ore' => 1000,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'CONTROLLO ACQUA NEL SERBATOIO',
            'ore' => 1000,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'CAMBIO ACQUA RADIATORE',
            'ore' => 1000,
        ]);

        DB::connection('db_agraria')->table('manutenzione_programmata')->insert([
            'nome' => 'CAMBIO OLIO',
            'ore' => 500,
        ]);
    }
}
