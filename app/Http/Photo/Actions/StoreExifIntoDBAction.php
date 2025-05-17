<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Photo\Models\ExifData;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\DB;

final class StoreExifIntoDBAction
{
    public function execute(string $jsonFile, ?string $prefixPathToRemove): int
    {
        $num = 0;
        $buffer = [];

        foreach (new JsonParser($jsonFile) as $value) {
            $data = ExifData::fromArray($value);
            $buffer[] = $data;
            if (count($buffer) >= 1000) {
                $this->insertBatch($buffer, $prefixPathToRemove);
                $buffer = [];
            }
            $num += 1;
        }
        if (count($buffer) > 0) {
            $this->insertBatch($buffer, $prefixPathToRemove);
        }

        return $num;
    }

    private function insertBatch(array $exifsData, ?string $prefixPathToRemove): void
    {
        $photoAttrs = collect();
        $shaToSubjects = [];

        foreach ($exifsData as $b) {
            $attrs = $b->toModelAttrs($prefixPathToRemove);
            $photoAttrs->add($attrs);
            if (count($b->subjects) > 0) {
                $shaToSubjects[$attrs['sha']] = $b->subjects;
            }
        }

        DB::connection('db_foto')->table('photos')->insert($photoAttrs->toArray());

        // Fetch the IDs for the inserted photos using their sha
        $shaList = array_keys($shaToSubjects);
        $photos = DB::connection('db_foto')->table('photos')
            ->whereIn('sha', $shaList)
            ->pluck('id', 'sha');

        $photoPeopleAttrs = collect();
        foreach ($shaToSubjects as $sha => $subjects) {
            $photoId = $photos[$sha] ?? null;
            if ($photoId) {
                foreach ($subjects as $name) {
                    $photoPeopleAttrs->push(['photo_id' => $photoId, 'persona_nome' => $name]);
                }
            }
        }

        if ($photoPeopleAttrs->count() > 0) {
            DB::connection('db_foto')->table('photos_people')->insert($photoPeopleAttrs->toArray());
        }
    }
}
