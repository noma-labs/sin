<?php

declare(strict_types=1);

namespace App\Photo\DataTransferObjects;

/**
 * The square to cut from a photo for an avatar, normalized to the photo so it does not depend on its size: around a
 * face region of the archive (1.8 times the larger side of the face, so the forehead, the chin and some hair fit,
 * raised by a tenth of the face) or the box drawn by hand on the photo. Once in pixels it is kept inside the photo.
 */
final readonly class RiquadroVolto
{
    public const MARGINE = 1.8;

    /**
     * @param  array{X: float, Y: float, W: float, H: float}  $area  the centre and the size of the box, normalized to the photo
     * @param  float  $margine  how many times the larger side of the box the square is
     * @param  float  $alzata  how much of the height of the box the square is raised by
     */
    private function __construct(private array $area, private float $margine, private float $alzata) {}

    /**
     * @param  array{X: float, Y: float, W: float, H: float}  $area  the centre and the size of the face, normalized to the photo (MWG)
     */
    public static function attornoAlVolto(array $area): self
    {
        return new self($area, self::MARGINE, 0.1);
    }

    /**
     * The box drawn on the photo, from its top-left corner, cut as it is.
     */
    public static function disegnato(float $x, float $y, float $larghezza, float $altezza): self
    {
        return new self(['X' => $x + $larghezza / 2, 'Y' => $y + $altezza / 2, 'W' => $larghezza, 'H' => $altezza], 1.0, 0.0);
    }

    /**
     * @return array{x: int, y: int, lato: int} the top-left corner and the side of the square in a photo of that size
     */
    public function inPixel(int $larghezza, int $altezza): array
    {
        $larghezzaBox = $this->area['W'] * $larghezza;
        $altezzaBox = $this->area['H'] * $altezza;
        $lato = max(1, min((int) round(max($larghezzaBox, $altezzaBox) * $this->margine), $larghezza, $altezza));

        $x = (int) round($this->area['X'] * $larghezza - $lato / 2);
        $y = (int) round($this->area['Y'] * $altezza - $altezzaBox * $this->alzata - $lato / 2);

        return [
            'x' => max(0, min($x, $larghezza - $lato)),
            'y' => max(0, min($y, $altezza - $lato)),
            'lato' => $lato,
        ];
    }

    /**
     * The square in a photo of that size, from its top-left corner and normalized to the photo, as the page draws it.
     *
     * @return array{x: float, y: float, w: float, h: float}
     */
    public function normalizzato(int $larghezza, int $altezza): array
    {
        $pixel = $this->inPixel($larghezza, $altezza);

        return [
            'x' => $pixel['x'] / $larghezza,
            'y' => $pixel['y'] / $altezza,
            'w' => $pixel['lato'] / $larghezza,
            'h' => $pixel['lato'] / $altezza,
        ];
    }
}
