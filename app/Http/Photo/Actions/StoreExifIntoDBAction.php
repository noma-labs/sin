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
        $photoPeopleAttrs = collect();

        foreach ($exifsData as $b) {
            $attrs = $b->toModelAttrs($prefixPathToRemove);
            $photoAttrs->add($attrs);
            if (count($b->subjects) > 0) {
                $persons = array_map(fn ($name): array => ['photo_id' => $attrs['uid'], 'persona_nome' => $name], $b->subjects);
                $photoPeopleAttrs->push(...$persons);
            }
        }

        DB::connection('db_foto')->table('photos')->insert($photoAttrs->toArray());
        DB::connection('db_foto')->table('photos_people')->insert($photoPeopleAttrs->toArray());
    }
}
