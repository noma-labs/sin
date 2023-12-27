<?php

namespace Domain\Photo\Actions;

use Cerbero\JsonParser\JsonParser;
use Domain\Photo\Models\ExifData;
use Illuminate\Support\Facades\DB;

class StoreExifIntoDBAction
{
    public function execute(string $jsonFile): int
    {
        $num  = 0;
        $buffer = [];
        foreach (new JsonParser($jsonFile) as $key => $value) {
            $data = ExifData::fromArray($value);
            $buffer[] = $data;
            if (count($buffer) >= 100) {
                $this->insertBuffer($buffer);
                $buffer = [];
            }
            $num += 1;
        }
        return $num;
    }

    private function insertBuffer(array $buffer): void
    {
        $attrs = array_map(fn ($d) => $d->toModelAttrs(), $buffer);
        DB::connection('db_foto')->table('photos')->insert($attrs);
    }
}
