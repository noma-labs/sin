<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Nomadelfia\Persona\Models\Persona;
use App\Photo\Models\DbfAll;
use App\Photo\Models\Photo;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;

final class PhotoTableSeeder extends Seeder
{
    public function run()
    {
        $faker = FakerFactory::create('it_IT');

        for ($i = 0; $i < 10; $i++) {
            $personas = Persona::factory()->count(3)->create();
            $personaNames = $personas->pluck('nome')->toArray();

            $photo = Photo::factory()
                ->withDetectedFaces(...$personaNames)
                ->create();

            // Attach personas with detected face names as persona_nome
            foreach ($personas as $index => $persona) {
                $photo->persone()->attach($persona->id, [
                    'persona_nome' => $personaNames[$index] ?? $persona->nome,
                ]);
            }

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
            $personas = Persona::factory()->count(2)->create();
            $personaNames = $personas->pluck('nome')->toArray();

            $photo = Photo::factory()
                ->withDetectedFaces(...$personaNames)
                ->inFolder($dirPath)
                ->create();

            // Attach personas with detected face names as persona_nome
            foreach ($personas as $index => $persona) {
                $photo->persone()->attach($persona->id, [
                    'persona_nome' => $personaNames[$index] ?? $persona->nome,
                ]);
            }

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

        // Create photos with issues
        $this->createPhotoWithIssue();

        Artisan::call('photos:detect-issues');
    }

    private function createPhotoWithIssue(): void
    {
        $faker = FakerFactory::create('it_IT');

        // Issue 1: Person not yet born
        $photoNotYetBorn = Photo::factory()
            ->withDetectedFaces('Mario Rossi')
            ->create(['taken_at' => '2010-05-15 10:30:00']);

        $personaNotYetBorn = Persona::factory()
            ->cognome('Rossi')
            ->nato(Carbon::parse('2015-06-20'))
            ->create(['nome' => 'Mario']);

        $photoNotYetBorn->persone()->attach($personaNotYetBorn->id, [
            'persona_nome' => 'Mario Rossi',
        ]);

        $dbfNotYetBorn = DbfAll::create([
            'fingerprint' => null,
            'source' => 'foto',
            'datnum' => '150510',
            'anum' => '150510',
            'cddvd' => 'CD000001',
            'hdint' => 'HDINT001',
            'hdext' => 'HDEXT001',
            'sc' => 'SC',
            'fi' => 'FI',
            'tp' => 'TP',
            'nfo' => 1,
            'data' => '2010-05-15',
            'localita' => $faker->city(),
            'argomento' => 'Test - Persona non ancora nata',
            'descrizione' => 'Test photo for person not yet born',
        ]);

        $photoNotYetBorn->dbf_id = $dbfNotYetBorn->id;
        $photoNotYetBorn->save();

        // Issue 2: Person already deceased
        $photoAlreadyDeceased = Photo::factory()
            ->withDetectedFaces('Anna Bianchi')
            ->create(['taken_at' => '2010-05-15 10:30:00']);

        $personaAlreadyDeceased = Persona::factory()
            ->cognome('Bianchi')
            ->nato(Carbon::parse('1920-03-10'))
            ->create([
                'nome' => 'Anna',
                'data_decesso' => '2005-12-25',
            ]);

        $photoAlreadyDeceased->persone()->attach($personaAlreadyDeceased->id, [
            'persona_nome' => 'Anna Bianchi',
        ]);

        $dbfAlreadyDeceased = DbfAll::create([
            'fingerprint' => null,
            'source' => 'foto',
            'datnum' => '150510',
            'anum' => '150510',
            'cddvd' => 'CD000001',
            'hdint' => 'HDINT001',
            'hdext' => 'HDEXT001',
            'sc' => 'SC',
            'fi' => 'FI',
            'tp' => 'TP',
            'nfo' => 1,
            'data' => '2010-05-15',
            'localita' => $faker->city(),
            'argomento' => 'Test - Persona già deceduta',
            'descrizione' => 'Test photo for already deceased person',
        ]);

        $photoAlreadyDeceased->dbf_id = $dbfAlreadyDeceased->id;
        $photoAlreadyDeceased->save();

        Artisan::call('photos:detect-issues');
    }
}
