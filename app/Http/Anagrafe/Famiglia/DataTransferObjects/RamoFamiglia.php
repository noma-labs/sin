<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\DataTransferObjects;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Support\Collection;

/**
 * A famiglia in the albero familiare formed by a persona as capofamiglia or moglie, with the coniuge (none for a
 * famiglia without coniuge, like a mamma di vocazione) and the figli.
 */
final readonly class RamoFamiglia
{
    /**
     * @param  Collection<int, Persona>  $figli  in order of birth, each one with the famiglieFormate and the famiglieComeFiglio loaded
     */
    public function __construct(
        public Famiglia $famiglia,
        public string $posizione,
        public ?Persona $coniuge,
        public Collection $figli,
    ) {}

    /**
     * The famiglie of a figlio accolto in more famiglie right before and right after this famiglia in his percorso.
     *
     * @return array{prima: ?Famiglia, poi: ?Famiglia}
     */
    public function famiglieVicine(Persona $figlio): array
    {
        $percorso = $figlio->famiglieComeFiglio->values();
        $indice = $percorso->search(fn (Famiglia $famiglia): bool => $famiglia->id === $this->famiglia->id);

        if ($indice === false) {
            return ['prima' => null, 'poi' => null];
        }

        return ['prima' => $percorso->get($indice - 1), 'poi' => $percorso->get($indice + 1)];
    }
}
