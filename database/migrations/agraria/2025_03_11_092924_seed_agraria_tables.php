<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $data = [
            ['nome' => '420/85 R30'],
            ['nome' => '320/85 R20'],
            ['nome' => '380/85 R30'],
            ['nome' => '11,2 R24'],
            ['nome' => '380/85 R28'],
            ['nome' => '420/70 R34'],
            ['nome' => '380/70 R24'],
            ['nome' => '600/65 R38'],
            ['nome' => '480/65 R28'],
            ['nome' => '360/70 R24'],
            ['nome' => '280/70 R16'],
        ];

        DB::connection('db_agraria')->table('gomma')->insert($data);

        $manutention = [
            ['nome' => 'VERIFICA OLIO MOTORE', 'ore' => 10],
            ['nome' => 'VERIFICA ACQUA RADIATORE', 'ore' => 10],
            ['nome' => 'PULIZIA FILTRO ARIA', 'ore' => 10],
            ['nome' => 'PULIZIA RADIATORE', 'ore' => 10],
            ['nome' => 'PULIZIA FILTRO ARIA CABINA', 'ore' => 100],
            ['nome' => 'INGRASSATURA GENERALE', 'ore' => 100],
            ['nome' => 'CONTROLLO PRESSIONE PNEUMATICI', 'ore' => 100],
            ['nome' => 'CONTROLLO LIVELLO OLIO FRENI', 'ore' => 100],
            ['nome' => 'CONTROLLO OLIO PONTE ANT E POST', 'ore' => 500],
            ['nome' => 'SOSTITUZIONE FILTRI GASOLIO', 'ore' => 500],
            ['nome' => 'SOSTITUZIONE FILTRO SERVIZI', 'ore' => 1000],
            ['nome' => 'SOSTITUZIONE FILTRO OLIO', 'ore' => 1000],
            ['nome' => 'SOSTITUZIONE FILTRO ARIA', 'ore' => 1000],
            ['nome' => 'CONTROLLO ACQUA NEL SERBATOIO', 'ore' => 1000],
            ['nome' => 'CAMBIO ACQUA RADIATORE', 'ore' => 1000],
            ['nome' => 'CAMBIO OLIO', 'ore' => 500],
        ];
        DB::connection('db_agraria')->table('manutenzione_programmata')->insert($manutention);
    }

    public function down(): void {}
};
