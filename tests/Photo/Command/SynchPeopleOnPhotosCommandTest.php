<?php

declare(strict_types=1);

use App\Nomadelfia\Persona\Models\Persona;
use App\Photo\Models\Photo;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\artisan;

it('synchronize people on photos command', function (): void {
    // Create the source record in alfa_enrico_15_feb_23
    DB::connection('db_nomadelfia')->table('alfa_enrico_15_feb_23')->insert([
        'id' => 1,
        'FOTO' => 'persona-nome-into-photo',
    ]);

    // Create a person linked to the alfa_enrico record
    $persona = Persona::factory()
        ->withIdEnrico(1)
        ->create([
            'id' => 99999,
            'nominativo' => 'a-nominativo',
            'nome' => 'a-nome',
            'cognome' => 'a-cognome',
            'data_nascita' => '2000-01-01',
            'sesso' => 'M',
        ]);

    // Create a photo with detected face region containing the person name
    $photo = Photo::factory()
        ->withDetectedFaces('persona-nome-into-photo')
        ->create([
            'sha' => 'test-sha',
            'source_file' => 'test.jpg',
        ]);

    // Link the photo to the person by name (without persona_id initially)
    DB::connection('db_foto')->table('photos_people')->insert([
        'photo_id' => $photo->id,
        'persona_id' => null,
        'persona_nome' => 'persona-nome-into-photo',
    ]);

    artisan('photos:sync-people')->assertExitCode(0);

    $row = DB::connection('db_foto')->table('photos_people')->where('persona_nome', 'persona-nome-into-photo')->first();
    expect($row->persona_id)->toBe(99999);
});
