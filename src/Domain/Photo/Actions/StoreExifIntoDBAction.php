<?php

namespace Domain\Photo\Actions;

use Domain\Photo\Models\ExifData;
use Illuminate\Support\Facades\DB;

class StoreExifIntoDBAction
{
    public function execute(string $jsonFile): int
    {
        $num  = 0;

        $stream = fopen($jsonFile, 'r');
        $listener = new \JsonStreamingParser\Listener\SimpleObjectQueueListener(function ($item) use (&$num): void {
            $data = ExifData::fromArray($item);
            $attrs = $data->toModelAttrs();
            DB::connection('db_foto')->table('photos')->insert($attrs);
            $num += 1;
        });
        try {
            $parser = new \JsonStreamingParser\Parser($stream, $listener);
            $parser->parse();
            fclose($stream);
        } catch (Exception $e) {
            fclose($stream);
            throw $e;
        }
        return $num;
    }
}
