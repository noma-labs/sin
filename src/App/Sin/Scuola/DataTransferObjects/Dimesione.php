<?php

namespace App\Scuola\DataTransferObjects;

use Exception;
use Illuminate\Support\Str;

class Dimensione
{
    public int $larghezza;

    public int $altezza;

    public function __construct(int $larghezza, int $altezza)
    {
        $this->larghezza = $larghezza;
        $this->altezza = $altezza;
    }

    public static function fromString(string $dimesione): Dimensione
    {
        $d = Str::of($dimesione)->explode('x');
        if (count($d) < 2) {
            throw new Exception('Dimensione deve essere nella forma 123x123 espresse in cemtimetri. Per esempio: 24x45');
        }
        $larghezza = $d[0];
        $altezza = $d[1];

        if (! is_numeric($larghezza) || ! is_numeric($altezza)) {
            throw new Exception('Le dimensioni devono essere un numero');
        }
        return new self((int)$larghezza, (int)$altezza);
    }

    public function toString(): string
    {
        return $this->larghezza.'x'.$this->altezza;
    }
}
