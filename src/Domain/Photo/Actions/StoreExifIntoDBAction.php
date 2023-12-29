<?php

namespace Domain\Photo\Actions;

use Cerbero\JsonParser\JsonParser;
use Domain\Photo\Models\ExifData;
use Domain\Photo\Models\Photo;
use Illuminate\Support\Facades\DB;

class StoreExifIntoDBAction
{
    public function execute(string $jsonFile): int
    {
        $num = 0;
        $buffer = [];
        foreach (new JsonParser($jsonFile) as $key => $value) {
            $data = ExifData::fromArray($value);
            $buffer[] = $data;
            if (count($buffer) >= 1000) {
                $this->insertBuffer($buffer);
                $buffer = [];
            }
            $num += 1;
        }
        if (count($buffer) >0) {
            $this->insertBuffer($buffer);
        }

        return $num;
    }

    private function insertBuffer(array $buffer): void
    {
//        foreach ($buffer as $photo) {
//            $photoAttrs = $photo->toModelAttrs();
//            $photoAttrs[] =
//            Photo::create($photoAttrs);
//            if (count($photo->subjects) > 0) {
//                $photoAttrs = array_map(fn($d) => ["photo_id"=>$photoAttrs['uid'], "persona_nome" =>$d], $photo->subjects);
//                DB::connection('db_foto')->table('foto_persone')->insert($photoAttrs);
//            }
//        }

        $photoAttrs = array_map(fn ($d) => $d->toModelAttrs(), $buffer);
        DB::connection('db_foto')->table('photos')->insert($photoAttrs);

        $photoPersone = array();
        foreach ($photoAttrs as $attrs){
            if (count($attrs->subjects) > 0) {
                $photoAttrs = array_map(fn($name) => ["photo_id"=>$attrs['uid'], "persona_nome" =>$name], $attrs->subjects);
                $photoPersone =  $photoPersone + $photoAttrs;
            }
        }
        DB::connection('db_foto')->table('foto_persone')->insert($photoPersone);
    }
}
