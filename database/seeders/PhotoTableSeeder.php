<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Nomadelfia\Persona\Models\Persona;
use App\Photo\Models\DbfAll;
use App\Photo\Models\Photo;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

final class PhotoTableSeeder extends Seeder
{
    public function run()
    {
        $faker = FakerFactory::create('it_IT');

        // Seed a base set of photos
        $photos = Photo::factory(10)
            ->has(Persona::factory()->count(3), 'persone')
            ->create();

        foreach ($photos as $photo) {
            $baseName = $photo->file_name;
            $datnum = null;
            if (str_contains($baseName, '-')) {
                $parts = explode('-', $baseName);
                $datnum = $parts[0] ?? null;
            } else {
                // Fallback: extract first 6 digits from filename, pad if needed
                $digits = preg_replace('/\D+/', '', (string) $baseName);
                $datnum = $digits !== '' ? mb_substr($digits, 0, 6) : mb_str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            }

            $dbf = DbfAll::create([
                'fingerprint' => null,
                'source' => Arr::random(['dia120', 'slide', 'foto']),
                'datnum' => $datnum,
                'anum' => $datnum,
                'cddvd' => 'CD000001',
                'hdint' => 'HDINT001',
                'hdext' => 'HDEXT001',
                'sc' => 'SC',
                'fi' => 'FI',
                'tp' => 'TP',
                'nfo' => 1,
                'data' => $photo->taken_at ? $photo->taken_at->format('Y-m-d') : null,
                'localita' => $faker->city(),
                'argomento' => $faker->sentence(6),
                'descrizione' => $faker->paragraph(),
            ]);

            $photo->dbf_id = $dbf->id;
            $photo->save();
        }

        // Seed specific directory tree examples to mimic production structure
        // Leading slash is ensured by the factory's inFolder() helper
        foreach ([
            'a',
            'a/b',
            'b/x',
            'ARCH GEN 41-50  (idem c. s)/Arch 44b',
        ] as $dirPath) {
            $photo = Photo::factory()
                ->has(Persona::factory()->count(2), 'persone')
                ->inFolder($dirPath)
                ->create();

            $baseName = $photo->file_name;
            if (str_contains($baseName, '-')) {
                $parts = explode('-', $baseName);
                $datnum = $parts[0] ?? null;
            } else {
                $digits = preg_replace('/\D+/', '', (string) $baseName);
                $datnum = $digits !== '' ? mb_substr($digits, 0, 6) : mb_str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            }

            $dbf = DbfAll::create([
                'fingerprint' => null,
                'source' => Arr::random(['dia120', 'slide', 'foto']),
                'datnum' => $datnum,
                'anum' => $datnum,
                'cddvd' => 'CD000001',
                'hdint' => 'HDINT001',
                'hdext' => 'HDEXT001',
                'sc' => 'SC',
                'fi' => 'FI',
                'tp' => 'TP',
                'nfo' => 1,
                'data' => $photo->taken_at ? $photo->taken_at->format('Y-m-d') : null,
                'localita' => $faker->city(),
                'argomento' => $faker->sentence(6),
                'descrizione' => $faker->paragraph(),
            ]);

            $photo->dbf_id = $dbf->id;
            $photo->save();
        }
    }
}
