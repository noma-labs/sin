<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

use function Pest\Laravel\artisan;

it('synchronize people on photos command', function (): void {
    DB::connection('db_nomadelfia')->table('alfa_enrico_15_feb_23')->insert([
        'id' => 1,
        'FOTO' => 'persona-nome-into-photo',
    ]);
    DB::connection('db_nomadelfia')->table('persone')->insert([
        'id' => 99999,
        'nominativo' => 'a-nominativo',
        'nome' => 'a-nome',
        'cognome' => 'a-cognome',
        'id_arch_pietro' => 0,
        'id_alfa_enrico' => 1,
        'data_nascita' => '2000-01-01',
        'sesso' => 'M',
    ]);
    $photoId = DB::connection('db_foto')->table('photos')->insertGetId([
        'sha' => 'test-sha',
        'source_file' => 'test.jpg',
    ]);
    DB::connection('db_foto')->table('photos_people')->insert([
        'photo_id' => $photoId,
        'persona_id' => null,
        'persona_nome' => 'persona-nome-into-photo',
    ]);

    artisan('photos:sync-people')->assertExitCode(0);

    $row = DB::connection('db_foto')->table('photos_people')->where('persona_nome', 'persona-nome-into-photo')->first();
    expect($row->persona_id)->toBe(99999);
});
