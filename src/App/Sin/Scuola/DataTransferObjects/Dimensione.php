<?php

declare(strict_types=1);

namespace App\Scuola\DataTransferObjects;

use Exception;
use Illuminate\Support\Str;

final class Dimensione
{
    public int $larghezza;

    public int $altezza;

    public static function fromString(?string $dimensione): ?self
    {
        if (is_null($dimensione)) {
            return null;
        }
        $d = Str::of($dimensione)->explode('x');
        if (count($d) < 2) {
            throw new Exception('Dimensione deve essere nella forma 123x456 espresse in centimetri. Per esempio: 21x29');
        }
        $larghezza = $d[0];
        $altezza = $d[1];

        if (! is_numeric($larghezza) || ! is_numeric($altezza)) {
            throw new Exception('Le dimensioni devono essere un numero');
        }
        $d = new self;
        $d->larghezza = (int) $larghezza;
        $d->altezza = (int) $altezza;

        return $d;
    }

    public function toString(): ?string
    {
        return $this->larghezza.'x'.$this->altezza;
    }
}
