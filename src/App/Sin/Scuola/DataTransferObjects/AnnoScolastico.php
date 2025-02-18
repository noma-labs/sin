<?php

declare(strict_types=1);

namespace App\Scuola\DataTransferObjects;

use App\Nomadelfia\Exceptions\SchoolException;
use Illuminate\Support\Str;
use Stringable;

final class AnnoScolastico implements Stringable
{
    public int $startYear;

    public int $endYear;

    public function __toString(): string
    {
        return $this->startYear.'/'.$this->endYear;
    }

    public static function fromString(string $as): self
    {
        $as = Str::of($as)->explode('/');
        if (count($as) < 2) {
            throw new SchoolException('Anno scolastico deve essere nella forma YYYY/ZZZZ. Per esempio: 2024/2025');
        }

        if (! is_numeric($as[0]) || ! is_numeric($as[1])) {
            throw new SchoolException('I due anni devono essere un numero');
        }
        $startYear = (int) $as[0];
        $endYear = (int) $as[1];

        if ($endYear !== $startYear + 1) {
            throw new SchoolException("Anno scolastico '$as' errato. La fine dell'anno scolastico deve essere consecutivo all'anno di inizio");
        }

        $a = new self;
        $a->startYear = $startYear;
        $a->endYear = $endYear;

        return $a;
    }
}
