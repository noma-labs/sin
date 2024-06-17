<?php

namespace Domain\Photo\Actions;

use Cerbero\JsonParser\JsonParser;
use Domain\Photo\Models\ExifData;
use Illuminate\Support\Facades\DB;

class StoreExifIntoDBAction
{
    public function execute(string $jsonFile): int
    {
        $num = 0;
        $buffer = [];
        foreach (new JsonParser($jsonFile) as $value) {
            $data = ExifData::fromArray($value);
            $buffer[] = $data;
            if (count($buffer) >= 1000) {
                $this->insertBatch($buffer);
                $buffer = [];
            }
            $num += 1;
        }
        if (count($buffer) > 0) {
            $this->insertBatch($buffer);
        }

        return $num;
    }

    private function insertBatch(array $exifsData): void
    {
        $photoAttrs = collect();
        $photoPeopleAttrs = collect();

        foreach ($exifsData as $b) {
            $attrs = $b->toModelAttrs();
            $photoAttrs->add($attrs);
            if (count($b->subjects) > 0) {
                $persons = array_map(fn ($name): array => ['photo_id' => $attrs['uid'], 'persona_nome' => $name], $b->subjects);
                $photoPeopleAttrs->push(...$persons);
            }
        }

        DB::connection('db_foto')->table('photos')->insert($photoAttrs->toArray());
        DB::connection('db_foto')->table('foto_persone')->insert($photoPeopleAttrs->toArray());
    }
}
