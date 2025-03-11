<?php

declare(strict_types=1);

use App\Scuola\Models\ClasseTipo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $connection = 'db_scuola';

    public function up(): void
    {
        $data = [
            ['id' => 1, 'nome' => ClasseTipo::PRESCUOLA_3ANNI, 'ciclo' => 'prescuola', 'ord' => 1, 'next' => 2],
            ['id' => 2, 'nome' => ClasseTipo::PRESCUOLA_4ANNI, 'ciclo' => 'prescuola', 'ord' => 2, 'next' => 3],
            ['id' => 3, 'nome' => ClasseTipo::PRESCUOLA_5ANNI, 'ciclo' => 'prescuola', 'ord' => 3, 'next' => 4],

            ['id' => 4, 'nome' => ClasseTipo::PRIMA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 4, 'next' => 5],
            ['id' => 5, 'nome' => ClasseTipo::SECONDA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 5, 'next' => 6],
            ['id' => 6, 'nome' => ClasseTipo::TERZA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 6, 'next' => 7],
            ['id' => 7, 'nome' => ClasseTipo::QUARTA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 7, 'next' => 8],
            ['id' => 8, 'nome' => ClasseTipo::QUINTA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 8, 'next' => 9],
            ['id' => 9, 'nome' => ClasseTipo::PRIMA_MEDIA, 'ciclo' => 'medie', 'ord' => 9, 'next' => 10],
            ['id' => 10, 'nome' => ClasseTipo::SECONDA_MEDIA, 'ciclo' => 'medie', 'ord' => 10, 'next' => 11],
            ['id' => 11, 'nome' => ClasseTipo::TERZA_MEDIA, 'ciclo' => 'medie', 'ord' => 11, 'next' => 12],

            ['id' => 12, 'nome' => '1 superiore', 'ciclo' => 'superiori', 'ord' => 30, 'next' => null],

            ['id' => 13, 'nome' => '1 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 31, 'next' => 14],
            ['id' => 14, 'nome' => '2 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 32, 'next' => 15],
            ['id' => 15, 'nome' => '3 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 33, 'next' => 16],
            ['id' => 16, 'nome' => '4 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 34, 'next' => 17],
            ['id' => 17, 'nome' => '5 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 35, 'next' => null],

            ['id' => 18, 'nome' => '1 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 36, 'next' => 19],
            ['id' => 19, 'nome' => '2 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 37, 'next' => 20],
            ['id' => 20, 'nome' => '3 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 38, 'next' => 21],
            ['id' => 21, 'nome' => '4 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 39, 'next' => 22],
            ['id' => 22, 'nome' => '5 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 40, 'next' => null],

            ['id' => 23, 'nome' => '1 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 41, 'next' => 24],
            ['id' => 24, 'nome' => '2 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 42, 'next' => 25],
            ['id' => 25, 'nome' => '3 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 43, 'next' => 26],
            ['id' => 26, 'nome' => '4 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 44, 'next' => 27],
            ['id' => 27, 'nome' => '5 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 45, 'next' => null],

            ['id' => 28, 'nome' => '1 L. SU.E.S.', 'ciclo' => 'superiori', 'ord' => 51, 'next' => null],
            ['id' => 29, 'nome' => 'Universita', 'ciclo' => 'universita', 'ord' => 70, 'next' => null],

        ];
        DB::connection('db_scuola')->table('tipo')->insert($data);
    }

    public function down(): void
    {
        //
    }
};
