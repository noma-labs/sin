<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Admin\Models\User;
use App\Anagrafe\Persona\Models\Persona;
use App\Photo\Models\Photo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('photos');
    Storage::fake('media_previews');
});

/**
 * A white photo of 1000x1000 on the photos disk with a red face of 150 px centred at (250, 300), tagged with $nome and
 * linked to $persona.
 */
function fotoDelVolto(Persona $persona, string $nome, ?string $scattataIl = '2012-05-06'): Photo
{
    $immagine = imagecreatetruecolor(1000, 1000);
    imagefill($immagine, 0, 0, imagecolorallocate($immagine, 255, 255, 255));
    imagefilledrectangle($immagine, 175, 225, 325, 375, imagecolorallocate($immagine, 255, 0, 0));
    ob_start();
    imagejpeg($immagine, null, 100);
    $percorso = 'volti/'.fake()->unique()->numberBetween(100000, 999999).'.jpg';
    Storage::disk('photos')->put($percorso, (string) ob_get_clean());

    $foto = Photo::factory()->create([
        'source_file' => $percorso,
        'image_width' => 1000,
        'image_height' => 1000,
        'taken_at' => $scattataIl,
        'region_info' => [
            'AppliedToDimensions' => ['W' => 1000, 'H' => 1000, 'Unit' => 'pixel'],
            'RegionList' => [
                ['Area' => ['X' => 0.25, 'Y' => 0.3, 'W' => 0.15, 'H' => 0.15, 'Unit' => 'normalized'], 'Name' => $nome, 'Rotation' => 0, 'Type' => 'Face'],
            ],
        ],
    ]);
    DB::connection('db_foto')->table('photos_people')->insert(['photo_id' => $foto->id, 'persona_id' => $persona->id, 'persona_nome' => $nome]);

    return $foto;
}

/**
 * @return array{0: int, 1: int, 2: int} the red, green and blue of a pixel of a JPEG
 */
function pixelDelVolto(string $jpeg, int $x, int $y): array
{
    $immagine = imagecreatefromstring($jpeg);
    $colore = imagecolorsforindex($immagine, imagecolorat($immagine, $x, $y));

    return [$colore['red'], $colore['green'], $colore['blue']];
}

it('lists the faces of the persona from the most recent photo, with the suggested one', function (): void {
    login();
    $persona = Persona::factory()->create(['nome' => 'Anna', 'cognome' => 'Rota']);
    $vecchia = fotoDelVolto($persona, 'ANNA RO', '1999-02-03');
    $recente = fotoDelVolto($persona, 'ANNA RO', '2014-09-10');

    $this->get(route('nomadelfia.person.avatar.volti', $persona->id))
        ->assertOk()
        ->assertSeeInOrder([
            'Scegli la foto di Anna Rota',
            route('nomadelfia.person.avatar.volto', ['id' => $persona->id, 'photo' => $recente->id, 'indice' => 0]), '10/09/2014', 'Consigliata',
            route('nomadelfia.person.avatar.volto', ['id' => $persona->id, 'photo' => $vecchia->id, 'indice' => 0]), '03/02/1999',
        ], false);
});

it('tells when the persona has no tagged face', function (): void {
    login();
    $persona = Persona::factory()->create();

    $this->get(route('nomadelfia.person.avatar.volti', $persona->id))
        ->assertOk()
        ->assertSee('Nessun volto taggato con il nome FOTO di questa persona.');
});

it('serves the face cut from the photo, kept for the next times', function (): void {
    login();
    $persona = Persona::factory()->create();
    $foto = fotoDelVolto($persona, 'ANNA RO');
    $indirizzo = route('nomadelfia.person.avatar.volto', ['id' => $persona->id, 'photo' => $foto->id, 'indice' => 0]);

    $this->get($indirizzo)->assertOk();

    $anteprima = Storage::disk('media_previews')->get("volti/{$foto->id}-0.jpg");
    expect(getimagesizefromstring($anteprima))->toMatchArray([0 => 160, 1 => 160])
        ->and(pixelDelVolto($anteprima, 80, 88)[1])->toBeLessThan(60);
    Storage::disk('photos')->delete($foto->source_file);
    $this->get($indirizzo)->assertOk();
});

it('saves the chosen face as the avatar, cut on the face', function (): void {
    login();
    $persona = Persona::factory()->create(['nome' => 'Anna', 'cognome' => 'Rota']);
    $foto = fotoDelVolto($persona, 'ANNA RO');

    $this->post(route('nomadelfia.person.avatar.volti.store', $persona->id), ['photo_id' => $foto->id, 'indice' => 0])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id))
        ->assertSessionHas('success', 'Foto di Anna Rota salvata.');

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => "persone/{$persona->id}.jpg"], 'db_nomadelfia');
    $avatar = Storage::disk('media_previews')->get("persone/{$persona->id}.jpg");
    expect(getimagesizefromstring($avatar))->toMatchArray([0 => 256, 1 => 256]);
    // the square of 270 px around the face starts at (115, 150): the face is in its middle, its corners are white
    [, $verdeCentro] = pixelDelVolto($avatar, 128, 141);
    [, $verdeAngolo] = pixelDelVolto($avatar, 5, 5);
    expect($verdeCentro)->toBeLessThan(60)->and($verdeAngolo)->toBeGreaterThan(200);
});

it('refuses the face of another persona', function (): void {
    login();
    $persona = Persona::factory()->create();
    $fotoDiUnAltro = fotoDelVolto(Persona::factory()->create(), 'PIETRO SA');

    $this->post(route('nomadelfia.person.avatar.volti.store', $persona->id), ['photo_id' => $fotoDiUnAltro->id, 'indice' => 0])
        ->assertSessionHasErrors('volto');
    $this->get(route('nomadelfia.person.avatar.volto', ['id' => $persona->id, 'photo' => $fotoDiUnAltro->id, 'indice' => 0]))
        ->assertNotFound();

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => null], 'db_nomadelfia');
});

it('forbids the faces to a user who cannot modify the persone or see the photos', function (string $permesso): void {
    $user = User::query()->firstOrCreate(['username' => "volti-solo-{$permesso}"], ['email' => "volti-solo-{$permesso}@email.it", 'password' => 'secret', 'persona_id' => 0]);
    $user->givePermissionTo($permesso);
    login($user);
    $persona = Persona::factory()->create();

    $this->get(route('nomadelfia.person.avatar.volti', $persona->id))->assertForbidden();
})->with([
    'without the photos' => ['popolazione.persona.modifica'],
    'without modifying the persone' => ['photo.view'],
]);

it('offers to draw the box on the photos of the persona without their tagged face', function (): void {
    login();
    $persona = Persona::factory()->create();
    $foto = fotoDelVolto($persona, 'ANNA RO');
    DB::connection('db_foto')->table('photos_people')->where('photo_id', $foto->id)->update(['persona_nome' => 'NOME DIVERSO']);

    $this->get(route('nomadelfia.person.avatar.volti', $persona->id))
        ->assertOk()
        ->assertSee('Nessun volto taggato con il nome FOTO di questa persona.')
        ->assertSee(route('nomadelfia.person.avatar.riquadro', ['id' => $persona->id, 'photo' => $foto->id]));
});

it('starts the box around the face to adjust', function (): void {
    login();
    $persona = Persona::factory()->create();
    $foto = fotoDelVolto($persona, 'ANNA RO');

    // the square of 270 px around the face starts at (115, 150)
    $this->get(route('nomadelfia.person.avatar.riquadro', ['id' => $persona->id, 'photo' => $foto->id, 'indice' => 0]))
        ->assertOk()
        ->assertSee(route('photos.preview', $foto->id))
        ->assertSee('{"x":0.115,"y":0.15,"w":0.27,"h":0.27}');
});

it('saves the box drawn on the photo as the avatar, cut as it is, with the time it was saved', function (): void {
    login();
    $persona = Persona::factory()->create(['nome' => 'Anna', 'cognome' => 'Rota']);
    $foto = fotoDelVolto($persona, 'ANNA RO');
    $this->travelTo('2026-11-26 10:30:00');

    // the red face: 150 px from (175, 225)
    $this->post(route('nomadelfia.person.avatar.riquadro.store', ['id' => $persona->id, 'photo' => $foto->id]), ['x' => 0.175, 'y' => 0.225, 'w' => 0.15, 'h' => 0.15])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id))
        ->assertSessionHas('success', 'Foto di Anna Rota salvata.');

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => "persone/{$persona->id}.jpg", 'avatar_aggiornato_il' => '2026-11-26 10:30:00'], 'db_nomadelfia');
    $avatar = Storage::disk('media_previews')->get("persone/{$persona->id}.jpg");
    expect(getimagesizefromstring($avatar))->toMatchArray([0 => 150, 1 => 150]);
    foreach ([[5, 5], [75, 75], [144, 144]] as [$x, $y]) {
        expect(pixelDelVolto($avatar, $x, $y)[1])->toBeLessThan(60);
    }
});

it('refuses a box that is missing or leaves the photo', function (array $box, string $errore, string $messaggio): void {
    login();
    $persona = Persona::factory()->create();
    $foto = fotoDelVolto($persona, 'ANNA RO');

    $this->post(route('nomadelfia.person.avatar.riquadro.store', ['id' => $persona->id, 'photo' => $foto->id]), $box)
        ->assertSessionHasErrors([$errore => $messaggio]);

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => null], 'db_nomadelfia');
})->with([
    'without the box' => [[], 'x', 'Disegna il riquadro intorno alla persona.'],
    'an empty box' => [['x' => 0.1, 'y' => 0.1, 'w' => 0, 'h' => 0], 'w', 'Disegna il riquadro intorno alla persona.'],
    'past the right edge' => [['x' => 0.9, 'y' => 0.1, 'w' => 0.2, 'h' => 0.2], 'riquadro', 'Il riquadro esce dalla foto.'],
]);

it('does not let the box be drawn on a photo of another persona', function (): void {
    login();
    $persona = Persona::factory()->create();
    $fotoDiUnAltro = fotoDelVolto(Persona::factory()->create(), 'PIETRO SA');

    $this->get(route('nomadelfia.person.avatar.riquadro', ['id' => $persona->id, 'photo' => $fotoDiUnAltro->id]))
        ->assertNotFound();
    $this->post(route('nomadelfia.person.avatar.riquadro.store', ['id' => $persona->id, 'photo' => $fotoDiUnAltro->id]), ['x' => 0.1, 'y' => 0.1, 'w' => 0.2, 'h' => 0.2])
        ->assertNotFound();

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => null], 'db_nomadelfia');
});

it('stays on the persona after the choice in the queue, to check the avatar before the next one', function (): void {
    login();
    $persona = Persona::factory()->create(['nome' => 'Anna', 'cognome' => 'Rota']);
    $foto = fotoDelVolto($persona, 'ANNA RO');
    $seconda = Persona::factory()->create();
    fotoDelVolto($seconda, 'PIETRO SA');

    $this->post(route('nomadelfia.person.avatar.volti.store', $persona->id), ['photo_id' => $foto->id, 'indice' => 0, 'coda' => 1])
        ->assertRedirect(route('nomadelfia.person.avatar.volti', ['id' => $persona->id, 'coda' => 1]))
        ->assertSessionHas('success', 'Foto di Anna Rota salvata. Controllala, poi passa alla prossima persona.');

    $this->get(route('nomadelfia.person.avatar.volti', ['id' => $persona->id, 'coda' => 1]))
        ->assertOk()
        ->assertSeeInOrder(['Prossima persona', 'Foto attuale'])
        ->assertSee(route('nomadelfia.person.avatar.volti', ['id' => $seconda->id, 'coda' => 1]));
});

it('goes back to the queue after the box of the last persona', function (): void {
    login();
    $ultima = Persona::factory()->create();
    $foto = fotoDelVolto($ultima, 'PIETRO SA');

    $this->post(route('nomadelfia.person.avatar.riquadro.store', ['id' => $ultima->id, 'photo' => $foto->id]), ['x' => 0.1, 'y' => 0.1, 'w' => 0.2, 'h' => 0.2, 'coda' => 1])
        ->assertRedirect(route('nomadelfia.person.avatar.volti', ['id' => $ultima->id, 'coda' => 1]));

    $this->get(route('nomadelfia.person.avatar.volti', ['id' => $ultima->id, 'coda' => 1]))
        ->assertOk()
        ->assertSeeInOrder(['Fine della coda', 'Foto attuale'])
        ->assertDontSee('Prossima persona');
});

it('skips to the next persona of the queue', function (): void {
    login();
    $prima = Persona::factory()->create();
    fotoDelVolto($prima, 'ANNA RO');
    $seconda = Persona::factory()->create();
    fotoDelVolto($seconda, 'PIETRO SA');

    $this->get(route('nomadelfia.person.avatar.volti', ['id' => $prima->id, 'coda' => 1]))
        ->assertOk()
        ->assertSee(route('nomadelfia.person.avatar.volti', ['id' => $seconda->id, 'coda' => 1]))
        ->assertSee('Salta');
});
