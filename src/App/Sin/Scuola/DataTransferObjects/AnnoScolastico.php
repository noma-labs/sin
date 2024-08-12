<?php

namespace App\Scuola\DataTransferObjects;

use Exception;
use Illuminate\Support\Str;

class AnnoScolastico
{
    public int $startYear;

    public int $endYear;

    public static function fromString(string $as): AnnoScolastico
    {
        $as = Str::of($as)->explode('/');
        if (count($as) < 2) {
            throw new Exception('Anno scolastico deve essere nella forma YYYY/YYYY. Per esempio: 2024/2025');
        }
        $startYear = $as[0];
        $endYear = $as[1];

        if (! is_numeric($startYear) || ! is_numeric($endYear)) {
            throw new Exception('I due anni devono essere un numero');
        }

        if( $endYear != $startYear +1){
            throw new Exception("La fine dell'anno scolastico deve essere consecutivo all'anno di inizio");
        }

        $a = new self();
        $a->startYear = (int) $startYear;
        $a->endYear = (int) $endYear;

        return $a;
    }

    public function toString(): string
    {
        return $this->startYear.'/'.$this->endYear;
    }
}
