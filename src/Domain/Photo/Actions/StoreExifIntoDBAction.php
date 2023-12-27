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
        foreach (new JsonParser($jsonFile) as $key => $value) {
            $data = ExifData::fromArray($value);
            $attrs = $data->toModelAttrs();
            DB::connection('db_foto')->table('photos')->insert($attrs);
            $num += 1;
        }
        return $num;
    }
}
