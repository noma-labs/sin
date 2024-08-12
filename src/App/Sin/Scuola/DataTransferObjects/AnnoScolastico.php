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
            throw new Exception('The input string must contain at least two parts separated by a slash. E.g., 2024/2025');
        }
        $startYear = $as[0];
        $endYear = $as[1];

        if (! is_numeric($startYear) || ! is_numeric($endYear)) {
            throw new Exception('Both parts of the input string must be numeric.');
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
