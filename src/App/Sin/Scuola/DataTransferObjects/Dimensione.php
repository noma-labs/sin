<?php

declare(strict_types=1);

namespace App\Scuola\DataTransferObjects;

use App\Scuola\Exceptions\BadDimensionException;
use Illuminate\Support\Str;
use Stringable;

final class Dimensione implements Stringable
{
    private int $width;

    private int $height;

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromString(?string $dimension): ?self
    {
        if (is_null($dimension)) {
            return null;
        }
        // trim possible 'mm' suffix
        $dimension = Str::of($dimension)->rtrim('mm')->toString();

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
        if (count($d) !== 2) {
            throw BadDimensionException::isNotValid($dimension, 'La dimensione deve essere nella forma LxH in millimetri. Per esempio: 210x297');
        }
        $width = $d[0];
        $height = $d[1];

        if (! is_numeric($width)) {
            throw BadDimensionException::isNotValid($dimension, 'La larghezza deve essere un numero intero');
        }
        if (! is_numeric($height)) {
            throw BadDimensionException::isNotValid($dimension, "l'altezza deve essere un numero intero");
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
