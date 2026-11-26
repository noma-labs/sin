<?php

declare(strict_types=1);

use App\Anagrafe\Persona\Models\Persona;
use App\Photo\Actions\VoltiPersonaAction;
use App\Photo\DataTransferObjects\RiquadroVolto;
use App\Photo\DataTransferObjects\Volto;
use App\Photo\Models\Photo;
use Illuminate\Support\Facades\DB;

/**
 * A photo 1000 px wide with the face regions $volti (name => [X, Y, W]) and the rows of photos_people $persone (persona_id => persona_nome).
 *
 * @param  array<string, array{0: float, 1: float, 2: float}>  $volti
 * @param  array<int, string>  $persone
 */
function fotoConVolti(array $volti, array $persone, ?string $scattataIl = '2010-06-01'): Photo
{
    $foto = Photo::factory()->create([
        'image_width' => 1000,
        'image_height' => 1000,
        'taken_at' => $scattataIl,
        'region_info' => [
            'AppliedToDimensions' => ['W' => 1000, 'H' => 1000, 'Unit' => 'pixel'],
            'RegionList' => array_map(fn (string $nome, array $area): array => [
                'Area' => ['X' => $area[0], 'Y' => $area[1], 'W' => $area[2], 'H' => $area[2], 'Unit' => 'normalized'],
                'Name' => $nome,
                'Rotation' => 0,
                'Type' => 'Face',
            ], array_keys($volti), $volti),
        ],
    ]);
    foreach ($persone as $personaId => $nome) {
        DB::connection('db_foto')->table('photos_people')->insert(['photo_id' => $foto->id, 'persona_id' => $personaId, 'persona_nome' => $nome]);
    }

    return $foto;
}

it('finds the faces of the persona in their photos, not the faces of the others', function (): void {
    $persona = Persona::factory()->create();
    $altro = Persona::factory()->create();
    $gruppo = fotoConVolti(['PIETRO SA' => [0.2, 0.3, 0.1], 'anna  ro' => [0.7, 0.3, 0.12]], [$persona->id => 'ANNA RO', $altro->id => 'PIETRO SA']);
    fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.1]], [$altro->id => 'ANNA RO']);

    $volti = resolve(VoltiPersonaAction::class)->execute($persona);

    expect($volti)->toHaveCount(1)
        ->and($volti[0])->toMatchArray(['photoId' => $gruppo->id, 'indice' => 1, 'latoPx' => 120])
        ->and($volti[0]->area)->toBe(['X' => 0.7, 'Y' => 0.3, 'W' => 0.12, 'H' => 0.12]);
});

it('orders the faces from the most recent photo, the photos without a date last', function (): void {
    $persona = Persona::factory()->create();
    $senzaData = fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.2]], [$persona->id => 'ANNA RO'], null);
    $vecchia = fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.2]], [$persona->id => 'ANNA RO'], '2001-03-04');
    $recente = fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.2]], [$persona->id => 'ANNA RO'], '2015-07-08');

    $volti = resolve(VoltiPersonaAction::class)->execute($persona);

    expect(array_map(fn (Volto $volto): int => $volto->photoId, $volti))->toBe([$recente->id, $vecchia->id, $senzaData->id]);
});

it('orders the faces large enough for the avatar first, then the small ones, each from the most recent', function (): void {
    $persona = Persona::factory()->create();
    $piccolaRecente = fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.1]], [$persona->id => 'ANNA RO'], '2020-01-01');
    $grandeVecchia = fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.3]], [$persona->id => 'ANNA RO'], '2001-01-01');
    $grandeRecente = fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.2]], [$persona->id => 'ANNA RO'], '2015-01-01');
    $piccolaVecchia = fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.05]], [$persona->id => 'ANNA RO'], '1990-01-01');

    $volti = resolve(VoltiPersonaAction::class)->execute($persona);

    expect(array_map(fn (Volto $volto): int => $volto->photoId, $volti))
        ->toBe([$grandeRecente->id, $grandeVecchia->id, $piccolaRecente->id, $piccolaVecchia->id]);
});

it('suggests the most recent face large enough for the avatar, or the largest one', function (array $lati, int $consigliato): void {
    $persona = Persona::factory()->create();
    foreach ($lati as $anno => $lato) {
        fotoConVolti(['ANNA RO' => [0.5, 0.5, $lato / 1000]], [$persona->id => 'ANNA RO'], "{$anno}-01-01");
    }

    $volti = resolve(VoltiPersonaAction::class)->execute($persona);

    expect(array_map(fn (Volto $volto): bool => $volto->consigliato, $volti))->toBe(array_map(fn (int $posizione): bool => $posizione === $consigliato, array_keys($volti)));
})->with([
    'a recent face too small' => [[2020 => 100, 2010 => 200, 2000 => 300], 0],
    'all the faces too small' => [[2020 => 50, 2010 => 90, 2000 => 70], 1],
]);

it('finds the photos of the persona without their tagged face', function (): void {
    $persona = Persona::factory()->create();
    fotoConVolti(['ANNA RO' => [0.5, 0.5, 0.2]], [$persona->id => 'ANNA RO']);
    $senzaVolto = fotoConVolti(['PIETRO SA' => [0.5, 0.5, 0.2]], [$persona->id => 'ANNA RO']);
    fotoConVolti(['PIETRO SA' => [0.5, 0.5, 0.2]], [Persona::factory()->create()->id => 'PIETRO SA']);

    $voltiPersona = resolve(VoltiPersonaAction::class);

    $foto = $voltiPersona->fotoSenzaVolto($persona, $voltiPersona->execute($persona))->get();

    expect($foto->modelKeys())->toBe([$senzaVolto->id]);
});

it('cuts a square around a face, kept inside the photo', function (array $area, array $riquadro): void {
    expect(RiquadroVolto::attornoAlVolto($area)->inPixel(1000, 1000))->toBe($riquadro);
})->with([
    'a face in the middle, raised by a tenth' => [['X' => 0.5, 'Y' => 0.5, 'W' => 0.1, 'H' => 0.1], ['x' => 410, 'y' => 400, 'lato' => 180]],
    'a face at the edge' => [['X' => 0.02, 'Y' => 0.02, 'W' => 0.1, 'H' => 0.1], ['x' => 0, 'y' => 0, 'lato' => 180]],
    'a face larger than the photo' => [['X' => 0.5, 'Y' => 0.5, 'W' => 0.9, 'H' => 0.9], ['x' => 0, 'y' => 0, 'lato' => 1000]],
]);

it('cuts the drawn box as a square on its larger side, kept inside the photo', function (array $box, array $riquadro): void {
    expect(RiquadroVolto::disegnato(...$box)->inPixel(2000, 1000))->toBe($riquadro);
})->with([
    'a square box' => [[0.1, 0.2, 0.05, 0.1], ['x' => 200, 'y' => 200, 'lato' => 100]],
    'a wide box, squared on its centre' => [[0.1, 0.2, 0.1, 0.1], ['x' => 200, 'y' => 150, 'lato' => 200]],
    'a box past the right edge' => [[0.98, 0.5, 0.05, 0.1], ['x' => 1900, 'y' => 500, 'lato' => 100]],
]);

it('gives the square normalized to the photo', function (): void {
    expect(RiquadroVolto::attornoAlVolto(['X' => 0.5, 'Y' => 0.5, 'W' => 0.1, 'H' => 0.1])->normalizzato(1000, 1000))
        ->toBe(['x' => 0.41, 'y' => 0.4, 'w' => 0.18, 'h' => 0.18]);
});
