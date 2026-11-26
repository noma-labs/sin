<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;
use App\Photo\Controllers\PhotoController;
use App\Photo\Models\Photo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\get;

it('index photos', function (): void {
    // the oldest date of the database: the index lists the photos from the oldest, so this one is on the first page
    // whatever the other tests leave in the database shared by the suite
    $photo = Photo::factory()->create(['taken_at' => '1000-01-01 00:00:00', 'file_name' => 'indice-'.fake()->uuid().'.jpg']);

    login();

    get(action([PhotoController::class, 'index']))
        ->assertSuccessful()
        ->assertSeeInOrder([$photo->file_name, '01/01/1000']);
});

it('a photo-ope roel can see only the photo card in the home page', function (): void {
    $photoOpe = User::create(['username' => 'i-am-an-user-for-photo', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $photoOpe->assignRole(Role::findByName('photo-ope'));

    login($photoOpe);

    get('/home')
        ->assertSuccessful()
        ->assertSee('Foto')
        ->assertDontSee('Biblioteca')
        ->assertDontSee('Popolazione')
        ->assertDontSee('Anagrafe')
        ->assertDontSee('Officina')
        ->assertDontSee('Amministratore')
        ->assertDontSee('Agraria')
        ->assertDontSee('Gestione Scuola')
        ->assertDontSee('Patenti');
});

it('show photo system to logged user', function (): void {
    $notSuperAdmin = User::create(['username' => 'i-am-an-user', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $notSuperAdmin->assignRole(Role::findByName('meccanica-amm'));

    login($notSuperAdmin);

    get(action([PhotoController::class, 'index']))
        ->assertForbidden();
});

it('shows folders page with directory nodes', function (): void {
    $inDir = Photo::factory()->inFolder('MyFolder')->create();
    $noDir = Photo::factory()->create(['directory' => null]);

    login();

    get(route('photos.folders.index'))
        ->assertSuccessful()
        ->assertSee('MyFolder');
});

it('shows only the photos linked to the persona when filtered by persona', function (): void {
    $persona = Persona::factory()->create(['nome' => 'Anna', 'cognome' => 'Verdi']);
    $conAnna = Photo::factory()->create();
    $conAnna->persone()->attach($persona->id, ['persona_nome' => 'ANNA V']);
    $conMarianna = Photo::factory()->create(['subjects' => 'MARIANNA']);
    $conMarianna->persone()->attach(Persona::factory()->create()->id, ['persona_nome' => 'MARIANNA']);
    login();

    $response = get(route('photos.index', ['persona' => $persona->id]));

    $response->assertSuccessful()
        ->assertSee('Foto di Anna Verdi')
        ->assertSee('href="'.route('photos.show', $conAnna->id).'"', false)
        ->assertDontSee('href="'.route('photos.show', $conMarianna->id).'"', false)
        ->assertSee(route('nomadelfia.person.profile', $persona->id));
});

it('links the people of a photo to their photos by persona and to their profile when they are linked to a persona', function (): void {
    $persona = Persona::factory()->create();
    $photo = Photo::factory()->create();
    $photo->persone()->attach($persona->id, ['persona_nome' => 'ANNA V']);
    DB::connection('db_foto')->table('photos_people')->insert(['photo_id' => $photo->id, 'persona_id' => null, 'persona_nome' => 'SCONOSCIUTO']);
    Storage::fake('photos')->put($photo->source_file, 'image');
    login();

    $response = get(route('photos.show', $photo->id));

    $response->assertSuccessful()
        ->assertSee(route('photos.index', ['persona' => $persona->id]))
        ->assertSee(route('nomadelfia.person.profile', $persona->id))
        ->assertSee(route('photos.index', ['name' => 'SCONOSCIUTO']));
});

it('shows also the photos with the name of the persona in the file ALFA not yet linked to a persona', function (): void {
    $idAlfa = fake()->unique()->numberBetween(100000, 999999);
    $nomeNelleFoto = 'PERSONA '.$idAlfa;
    DB::connection('db_nomadelfia')->table('alfa_enrico_15_feb_23')->insert(['id' => $idAlfa, 'FOTO' => $nomeNelleFoto]);
    $persona = Persona::factory()->withIdEnrico($idAlfa)->create();
    $nonCollegata = Photo::factory()->create();
    $diUnAltro = Photo::factory()->create();
    $nomeSimile = Photo::factory()->create();
    DB::connection('db_foto')->table('photos_people')->insert([
        ['photo_id' => $nonCollegata->id, 'persona_id' => null, 'persona_nome' => $nomeNelleFoto],
        ['photo_id' => $diUnAltro->id, 'persona_id' => Persona::factory()->create()->id, 'persona_nome' => $nomeNelleFoto],
        ['photo_id' => $nomeSimile->id, 'persona_id' => null, 'persona_nome' => $nomeNelleFoto.'0'],
    ]);
    login();

    $response = get(route('photos.index', ['persona' => $persona->id]));

    $response->assertSuccessful()
        ->assertSee('href="'.route('photos.show', $nonCollegata->id).'"', false)
        ->assertDontSee('href="'.route('photos.show', $diUnAltro->id).'"', false)
        ->assertDontSee('href="'.route('photos.show', $nomeSimile->id).'"', false);
});
