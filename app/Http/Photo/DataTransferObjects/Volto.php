<?php

declare(strict_types=1);

namespace App\Photo\DataTransferObjects;

use DateTimeInterface;

/**
 * A face of a persona tagged in a photo of the archive: a face region (MWG) of photos.region_info with the name of the
 * persona in the photos.
 */
final readonly class Volto
{
    /**
     * @param  int  $indice  the position of the region in the RegionList of the photo
     * @param  array{X: float, Y: float, W: float, H: float}  $area  the centre and the size of the face, normalized to the photo
     * @param  int  $latoPx  the width of the face in pixels in the original photo
     * @param  bool  $consigliato  whether it is the face suggested as avatar
     */
    public function __construct(
        public int $photoId,
        public int $indice,
        public string $sourceFile,
        public array $area,
        public int $latoPx,
        public ?DateTimeInterface $data,
        public bool $consigliato = false,
    ) {}

    /**
     * The square to cut around the face for the avatar.
     */
    public function riquadro(): RiquadroVolto
    {
        return RiquadroVolto::attornoAlVolto($this->area);
    }

    public function conConsigliato(bool $consigliato): self
    {
        return new self($this->photoId, $this->indice, $this->sourceFile, $this->area, $this->latoPx, $this->data, $consigliato);
    }
}
