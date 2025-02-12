<?php

declare(strict_types=1);

namespace App\Scuola\DataTransferObjects;

use Exception;
use Illuminate\Support\Str;

final class Dimensione
{
    private int $width;

    private int $height;

    public static function fromString(?string $dimension): ?self
    {
        if (is_null($dimension)) {
            return null;
        }
        // trim possible 'mm' suffix
        $dimension = Str::of($dimension)->rtrim('mm');

        if (Str::lower($dimension) === 'a4') {
            $d = new self;
            $d->width = 210;
            $d->height = 297;

            return $d;
        }
        if (Str::lower($dimension) === 'a3') {
            $d = new self;
            $d->width = 297;
            $d->height = 420;

            return $d;
        }
        $d = Str::of($dimension)->explode('x');
        if (count($d) < 2) {
            throw new Exception('Il formato della dimensione non è corretto');
        }
        $width = $d[0];
        $height = $d[1];

        if (! is_numeric($width)) {
            throw new Exception('Dimensione incorretta. La largehzza deve essere un numero intero');
        }
        if (! is_numeric($height)) {
            throw new Exception('Dimensione incorretta. Altezza deve essere un numero intero');
        }

        $d = new self;
        $d->width = (int) $width;
        $d->height = (int) $height;

        return $d;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function toString(): string
    {
        return $this->width.'x'.$this->height;
    }
}
