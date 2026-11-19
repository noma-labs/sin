<?php

declare(strict_types=1);

use App\Anagrafe\Persona\Actions\ImportAvatarDecessiAction;
use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('media_originals');
    Storage::fake('media_previews');
});

/**
 * A nome made only of letters and unique in the test database, which is not reset between tests.
 */
function nomeUnico(): string
{
    return ucfirst(fake()->unique()->lexify('??????????'));
}

/**
 * A JPEG of $larghezza x $altezza, white with the rows from $righeBlu painted blue and the rows from $righeRosse painted red.
 *
 * @param  array{0: int, 1: int}|null  $righeBlu
 * @param  array{0: int, 1: int}|null  $righeRosse
 */
function jpegDecesso(int $larghezza, int $altezza, ?array $righeBlu = null, ?array $righeRosse = null): string
{
    $immagine = imagecreatetruecolor($larghezza, $altezza);
    imagefill($immagine, 0, 0, imagecolorallocate($immagine, 255, 255, 255));
    if ($righeBlu !== null) {
        imagefilledrectangle($immagine, 0, $righeBlu[0], $larghezza - 1, $righeBlu[1], imagecolorallocate($immagine, 0, 0, 255));
    }
    if ($righeRosse !== null) {
        imagefilledrectangle($immagine, 0, $righeRosse[0], $larghezza - 1, $righeRosse[1], imagecolorallocate($immagine, 255, 0, 0));
    }
    ob_start();
    imagejpeg($immagine, null, 100);

    return (string) ob_get_clean();
}

/**
 * Writes the reviewed CSV with the columns read by the import.
 *
 * @param  list<array{file: string, persona_id: int|string, esito?: string}>  $righe
 */
function csvAvatar(array $righe): string
{
    $file = tempnam(sys_get_temp_dir(), 'avatar-');
    $handle = fopen($file, 'w');
    fputcsv($handle, ['file', 'mese', 'nome_file', 'persona_id', 'candidati', 'esito'], escape: '');
    foreach ($righe as $riga) {
        fputcsv($handle, [$riga['file'], '', '', (string) $riga['persona_id'], '', $riga['esito'] ?? 'OK'], escape: '');
    }
    fclose($handle);

    return $file;
}

/**
 * @return array<string, array<string, string>> the rows of the CSV of the suggestions, by file
 */
function leggiSuggerimenti(string $file): array
{
    $righe = array_map(fn (string $linea): array => str_getcsv($linea, escape: ''), file($file, FILE_IGNORE_NEW_LINES));
    $intestazione = array_shift($righe);

    return collect($righe)->map(fn (array $riga): array => array_combine($intestazione, $riga))->keyBy('file')->all();
}

/**
 * The red, green and blue of a pixel of the avatar saved on the media_previews disk.
 *
 * @return array{0: int, 1: int, 2: int}
 */
function colore(string $percorso, int $x, int $y): array
{
    $immagine = imagecreatefromstring(Storage::disk('media_previews')->get($percorso));
    $colore = imagecolorsforindex($immagine, imagecolorat($immagine, $x, $y));

    return [$colore['red'], $colore['green'], $colore['blue']];
}

it('suggests the deceased of the month with the name of each photo, leaving the doubtful ones to review', function (): void {
    $nomeUnico = nomeUnico();
    $unico = Persona::factory()->deceduta(Carbon::parse('2019-04-12'))->create(['nome' => $nomeUnico]);
    $nomeOmonimi = nomeUnico();
    $omonimo = Persona::factory()->nato(Carbon::parse('1960-05-08'))->deceduta(Carbon::parse('2021-05-12'))->create(['nome' => $nomeOmonimi]);
    $altroOmonimo = Persona::factory()->deceduta(Carbon::parse('2003-05-02'))->create(['nome' => $nomeOmonimi]);
    $disco = Storage::disk('media_originals');
    $disco->put("decessi/DECESSI4 APRILE/{$nomeUnico} 46028-12.jpg", jpegDecesso(10, 10));
    $disco->put("decessi/DECESSI4 APRILE/{$nomeUnico} Q.jpg", jpegDecesso(10, 10));
    $disco->put("decessi/DECESSI5 MAGGIO/{$nomeOmonimi}.jpg", jpegDecesso(10, 10));
    $disco->put("decessi/DECESSI5 MAGGIO/{$nomeOmonimi} 08.05.1960 - 12.05.2021.jpg", jpegDecesso(10, 10));
    $disco->put("decessi/DECESSI6  GIUGNO/{$nomeUnico}.jpg", jpegDecesso(10, 10));
    $disco->put('decessi/DECESSI6  GIUGNO/353A3296.jpg', jpegDecesso(10, 10));
    $disco->put('decessi/DECESSI6  GIUGNO/Thumbs.db', 'not a photo');
    $file = tempnam(sys_get_temp_dir(), 'suggerimenti-');

    $this->artisan('persone:import-avatar-decessi', ['--suggest' => $file])
        ->expectsTable(['Esito', 'Foto'], [['OK', 2], ['AMBIGUO', 2], ['NON_TROVATO', 2]])
        ->assertSuccessful();

    $righe = leggiSuggerimenti($file);
    expect($righe)->toHaveCount(6)
        ->and($righe["decessi/DECESSI4 APRILE/{$nomeUnico} 46028-12.jpg"])->toMatchArray(['mese' => '4', 'persona_id' => (string) $unico->id, 'esito' => 'OK'])
        ->and($righe["decessi/DECESSI4 APRILE/{$nomeUnico} Q.jpg"])->toMatchArray(['persona_id' => '', 'esito' => 'AMBIGUO'])
        ->and($righe["decessi/DECESSI5 MAGGIO/{$nomeOmonimi}.jpg"])->toMatchArray(['persona_id' => '', 'esito' => 'AMBIGUO'])
        ->and($righe["decessi/DECESSI5 MAGGIO/{$nomeOmonimi}.jpg"]['candidati'])->toContain("{$omonimo->id}:")->toContain("{$altroOmonimo->id}:")
        ->and($righe["decessi/DECESSI5 MAGGIO/{$nomeOmonimi} 08.05.1960 - 12.05.2021.jpg"])->toMatchArray(['persona_id' => (string) $omonimo->id, 'esito' => 'OK'])
        ->and($righe["decessi/DECESSI6  GIUGNO/{$nomeUnico}.jpg"])->toMatchArray(['persona_id' => '', 'candidati' => '', 'esito' => 'NON_TROVATO'])
        ->and($righe['decessi/DECESSI6  GIUGNO/353A3296.jpg'])->toMatchArray(['nome_file' => '', 'esito' => 'NON_TROVATO']);
    Storage::disk('media_previews')->assertDirectoryEmpty('/');
});

it('leaves to review a persona suggested for more than one photo', function (): void {
    $nome = nomeUnico();
    Persona::factory()->deceduta(Carbon::parse('2010-02-03'))->create(['nome' => $nome]);
    Storage::disk('media_originals')->put("decessi/DECESSI2 FEBBRAIO/{$nome}.jpg", jpegDecesso(10, 10));
    Storage::disk('media_originals')->put("decessi/DECESSI2 FEBBRAIO/{$nome} (2).jpg", jpegDecesso(10, 10));
    $file = tempnam(sys_get_temp_dir(), 'suggerimenti-');

    $this->artisan('persone:import-avatar-decessi', ['--suggest' => $file])->assertSuccessful();

    expect(array_column(leggiSuggerimenti($file), 'esito'))->toBe(['AMBIGUO', 'AMBIGUO']);
});

it('reads the nome, the surname initial and the dates from the name of a photo', function (string $nomeFile, array $atteso): void {
    expect(resolve(ImportAvatarDecessiAction::class)->analizzaNomeFile($nomeFile))->toBe($atteso);
})->with([
    'a plain nome' => ['Maria.jpg', ['nome' => 'maria', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
    'a surname initial and a photo code' => ['Anna S 44105-02.jpg', ['nome' => 'anna', 'iniziale' => 's', 'nascita' => null, 'decesso' => null]],
    'a surname initial glued to the nome' => ['AntonioS.jpg', ['nome' => 'antonio', 'iniziale' => 's', 'nascita' => null, 'decesso' => null]],
    'the dates of nascita and decesso' => ['ALDO 08.05.1960 - 12.04.2021.jpg', ['nome' => 'aldo', 'iniziale' => null, 'nascita' => '1960-05-08', 'decesso' => '2021-04-12']],
    'the dates after an underscore' => ['Alda_16.12.1939 - 23.08.2021.jpg', ['nome' => 'alda', 'iniziale' => null, 'nascita' => '1939-12-16', 'decesso' => '2021-08-23']],
    'the title Don and a code glued to the nome' => ['DON ZENOS2213.jpg', ['nome' => 'zeno', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
    'the title P. and an edited copy' => ['P. Bernardo_modificato-1.jpg', ['nome' => 'bernardo', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
    'a letter after the dates' => ['Gianni 04.09.1934 - 07.11.2020i.jpg', ['nome' => 'gianni', 'iniziale' => null, 'nascita' => '1934-09-04', 'decesso' => '2020-11-07']],
    'a camera code after the nome' => ['SIstilia IMG_1497.jpg', ['nome' => 'sistilia', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
    'a copy number' => ['Giovanni (2).jpg', ['nome' => 'giovanni', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
    'a trailing digit' => ['Giuseppe3.jpg', ['nome' => 'giuseppe', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
    'an edited copy' => ['Isabella 27508b03_modificato-2.jpg', ['nome' => 'isabella', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
    'nome and cognome' => ['Anna Albertoni Pirelli .jpg', ['nome' => 'anna albertoni pirelli', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
    'the name of the camera' => ['B70A5868.jpg', ['nome' => '', 'iniziale' => null, 'nascita' => null, 'decesso' => null]],
]);

it('saves the photos of the rows to import as a square avatar, skipping the other rows', function (): void {
    $grande = Persona::factory()->deceduta(Carbon::parse('2015-04-12'))->create();
    $piccola = Persona::factory()->deceduta(Carbon::parse('2016-04-01'))->create();
    $daRivedere = Persona::factory()->deceduta(Carbon::parse('2017-04-01'))->create();
    $disco = Storage::disk('media_originals');
    $disco->put('decessi/DECESSI4 APRILE/Grande.jpg', jpegDecesso(300, 400));
    $disco->put('decessi/DECESSI4 APRILE/Piccola.jpg', jpegDecesso(100, 200, righeBlu: [0, 20], righeRosse: [130, 199]));
    $disco->put('decessi/DECESSI4 APRILE/Altra.jpg', jpegDecesso(10, 10));
    $file = csvAvatar([
        ['file' => 'decessi/DECESSI4 APRILE/Grande.jpg', 'persona_id' => $grande->id],
        ['file' => 'decessi/DECESSI4 APRILE/Piccola.jpg', 'persona_id' => $piccola->id],
        ['file' => 'decessi/DECESSI4 APRILE/Altra.jpg', 'persona_id' => $daRivedere->id, 'esito' => 'AMBIGUO'],
    ]);

    $this->artisan('persone:import-avatar-decessi', ['file' => $file])
        ->expectsTable(['Avatar importati', 'Righe ignorate'], [[2, 1]])
        ->assertSuccessful();

    $this->assertDatabaseHas('persone', ['id' => $grande->id, 'avatar_path' => "persone/{$grande->id}.jpg"], 'db_nomadelfia');
    $this->assertDatabaseHas('persone', ['id' => $piccola->id, 'avatar_path' => "persone/{$piccola->id}.jpg"], 'db_nomadelfia');
    $this->assertDatabaseHas('persone', ['id' => $daRivedere->id, 'avatar_path' => null], 'db_nomadelfia');
    $previews = Storage::disk('media_previews');
    expect(getimagesizefromstring($previews->get("persone/{$grande->id}.jpg")))->toMatchArray([0 => 256, 1 => 256, 'mime' => 'image/jpeg'])
        ->and(getimagesizefromstring($previews->get("persone/{$piccola->id}.jpg")))->toMatchArray([0 => 100, 1 => 100]);
    // the square of the portrait starts a quarter of the extra height from the top (row 25): above the red rows, below the blue ones
    foreach ([colore("persone/{$piccola->id}.jpg", 50, 1), colore("persone/{$piccola->id}.jpg", 50, 98)] as [$rosso, $verde, $blu]) {
        expect($rosso)->toBeGreaterThan(200)->and($verde)->toBeGreaterThan(200)->and($blu)->toBeGreaterThan(200);
    }
    $previews->assertMissing("persone/{$daRivedere->id}.jpg");
});

it('saves nothing with the dry run, showing what would be imported', function (): void {
    $persona = Persona::factory()->deceduta(Carbon::parse('2015-04-12'))->create();
    Storage::disk('media_originals')->put('decessi/DECESSI4 APRILE/Foto.jpg', jpegDecesso(10, 10));
    $file = csvAvatar([['file' => 'decessi/DECESSI4 APRILE/Foto.jpg', 'persona_id' => $persona->id]]);

    $this->artisan('persone:import-avatar-decessi', ['file' => $file, '--dry-run' => true])
        ->expectsTable(['Avatar importati', 'Righe ignorate'], [[1, 0]])
        ->assertSuccessful();

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => null], 'db_nomadelfia');
    Storage::disk('media_previews')->assertDirectoryEmpty('/');
});

it('replaces an existing avatar with --force', function (): void {
    $persona = Persona::factory()->deceduta(Carbon::parse('2015-04-12'))->create(['avatar_path' => 'persone/vecchio.jpg']);
    Storage::disk('media_originals')->put('decessi/DECESSI4 APRILE/Foto.jpg', jpegDecesso(10, 10));
    $file = csvAvatar([['file' => 'decessi/DECESSI4 APRILE/Foto.jpg', 'persona_id' => $persona->id]]);

    $this->artisan('persone:import-avatar-decessi', ['file' => $file, '--force' => true])->assertSuccessful();

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => "persone/{$persona->id}.jpg"], 'db_nomadelfia');
    Storage::disk('media_previews')->assertExists("persone/{$persona->id}.jpg");
});

it('refuses the whole file when a row to import is wrong', function (Closure $riga, string $problema): void {
    $persona = Persona::factory()->deceduta(Carbon::parse('2015-04-12'))->create();
    Storage::disk('media_originals')->put('decessi/DECESSI4 APRILE/Foto.jpg', jpegDecesso(10, 10));
    Storage::disk('media_originals')->put('decessi/DECESSI4 APRILE/Altra.jpg', jpegDecesso(10, 10));
    $file = csvAvatar([
        ['file' => 'decessi/DECESSI4 APRILE/Foto.jpg', 'persona_id' => $persona->id],
        $riga($persona),
    ]);

    $this->artisan('persone:import-avatar-decessi', ['file' => $file])
        ->expectsOutputToContain($problema)
        ->assertFailed();

    $this->assertDatabaseHas('persone', ['id' => $persona->id, 'avatar_path' => null], 'db_nomadelfia');
    Storage::disk('media_previews')->assertDirectoryEmpty('/');
})->with([
    'a persona that does not exist' => [fn (): array => ['file' => 'decessi/DECESSI4 APRILE/Altra.jpg', 'persona_id' => 99999999], 'riga 3: la persona `99999999` non esiste'],
    'a persona not deceased' => [fn (): array => ['file' => 'decessi/DECESSI4 APRILE/Altra.jpg', 'persona_id' => Persona::factory()->create()->id], 'non è deceduta'],
    'a persona with an avatar' => [fn (): array => ['file' => 'decessi/DECESSI4 APRILE/Altra.jpg', 'persona_id' => Persona::factory()->deceduta(Carbon::parse('2015-04-01'))->create(['avatar_path' => 'persone/1.jpg'])->id], 'ha già un avatar (usa --force per sostituirlo)'],
    'the same persona twice' => [fn (Persona $persona): array => ['file' => 'decessi/DECESSI4 APRILE/Altra.jpg', 'persona_id' => $persona->id], 'è già nella riga 2'],
    'a file that does not exist' => [fn (): array => ['file' => 'decessi/DECESSI4 APRILE/Manca.jpg', 'persona_id' => Persona::factory()->deceduta(Carbon::parse('2015-04-01'))->create()->id], 'riga 3: il file `decessi/DECESSI4 APRILE/Manca.jpg` non esiste'],
]);

it('refuses a file without the columns of the suggestions', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'avatar-');
    file_put_contents($file, "persona_id\n1\n");

    $this->artisan('persone:import-avatar-decessi', ['file' => $file])
        ->expectsOutputToContain('Missing columns in the CSV: file, esito')
        ->assertFailed();
});
