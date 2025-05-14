<?php

declare(strict_types=1);

use App\Photo\Models\Photo;
use App\Photo\Models\RegionInfo;
use App\Photo\Models\RegionInfoRegion;

it('reads the json and import into the db', function (): void {
    $this->artisan('exif:import', ['file' => 'tests/Photo/Command/testfile/photos.json'])
        ->expectsOutputToContain('Inserted 2 photos')
        ->assertSuccessful();

    $photo1 = Photo::query()->where('sha', 'c664dd273f4302db47d5cd12702bb242')->first();
    expect($photo1)->not->toBeNull();

    expect($photo1->source_file)->toBe('/var/www/html/storage/app/foto-sport/2017-04-17 XF Sport nom.fi Batignano/IMG_0107.JPG');
    expect($photo1->file_name)->toBe('IMG_0107.JPG');
    expect($photo1->directory)->toBe('/var/www/html/storage/app/foto-sport/2017-04-17 XF Sport nom.fi Batignano');
    expect($photo1->file_size)->toBe(2905070);
    expect($photo1->mime_type)->toBe('image/jpeg');
    expect($photo1->image_width)->toBe(3648);
    expect($photo1->image_height)->toBe(2736);
    expect($photo1->subjects)->toBe('DAVIDE DS,MARCO GL');
    expect($photo1->taken_at->format('Y-m-d H:i:s'))->toBe('2017-04-17 14:30:38');
    expect($photo1->region_info)->toEqual(new RegionInfo(
        [
            'H' => 2736,
            'Unit' => 'pixel',
            'W' => 3648,
        ],
        [
            new RegionInfoRegion(
                ['H' => 0.0595, 'W' => 0.04462, 'X' => 0.0714, 'Y' => 0.51507],
                'MARCO GL',
                0.01564,
                'Face'
            ),
            new RegionInfoRegion(
                ['H' => 0.05785, 'W' => 0.04339, 'X' => 0.66375, 'Y' => 0.53962],
                'DAVIDE DS',
                -0.07985,
                'Face'
            ),
        ]
    ));

    // Check second photo
    $photo2 = Photo::query()->where('sha', '4440e1d88fa019597e124a2b1ef32b5d')->first();
    expect($photo2)->not->toBeNull();
    expect($photo2->file_name)->toBe('IMG_0110.JPG');
    expect($photo2->image_width)->toBe(3648);
    expect($photo2->image_height)->toBe(2736);
    expect($photo2->region_info)->toEqual(new RegionInfo(
        [
            'H' => 2736,
            'Unit' => 'pixel',
            'W' => 3648,
        ],
        [
            new RegionInfoRegion(
                ['H' => 0.05785, 'W' => 0.04339, 'X' => 0.07561, 'Y' => 0.38307],
                'IMMIGRATI BATIGNANO',
                0.23,
                'Face'
            ),
            new RegionInfoRegion(
                ['H' => 0.05785, 'W' => 0.04339, 'X' => 0.35021, 'Y' => 0.51512],
                'PIERANGELO GA',
                0,
                'Face'
            ),
            new RegionInfoRegion(
                ['H' => 0.05785, 'W' => 0.04339, 'X' => 0.84018, 'Y' => 0.5527],
                'DAMIANO CM',
                0.23,
                'Face'
            ),
            new RegionInfoRegion(
                ['H' => 0.06069, 'W' => 0.03575, 'X' => 0.78819, 'Y' => 0.43642],
                'DANIELE LS',
                0,
                'Face'
            ),
            new RegionInfoRegion(
                ['H' => 0.05202, 'W' => 0.03467, 'X' => 0.46587, 'Y' => 0.44798],
                'CARLO SD',
                0,
                'Face'
            ),
            new RegionInfoRegion(
                ['H' => 0.06069, 'W' => 0.05417, 'X' => 0.59263, 'Y' => 0.55491],
                'MANUEL GL',
                0,
                'Face'
            ),
            new RegionInfoRegion(
                ['H' => 0.07225, 'W' => 0.05525, 'X' => 0.21506, 'Y' => 0.40751],
                'DAVIDE AC',
                0,
                'Face'
            ),
            new RegionInfoRegion(
                ['H' => 0.05636, 'W' => 0.03467, 'X' => 0.33369, 'Y' => 0.43569],
                'ANDREA MC',
                0,
                'Face'
            ),
        ]
    ));
});
