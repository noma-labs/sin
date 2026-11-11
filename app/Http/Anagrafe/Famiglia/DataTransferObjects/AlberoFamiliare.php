<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\DataTransferObjects;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

/**
 * The albero familiare of a persona, top down: the nonni above each genitore, the genitori (the capofamiglia and the
 * moglie of the famiglia d'origine), then the figli of the famiglia d'origine (the persona and the fratelli) as rami.
 * A persona accolta in more famiglie has a percorso of famiglie d'origine: the albero is drawn from one of them.
 * Without a famiglia d'origine there are no genitori and the only ramo is the persona.
 */
final readonly class AlberoFamiliare
{
    /**
     * @param  Collection<int, Famiglia>  $percorso  the famiglie of the persona as figlio, from the first one, each one with the pivot
     * @param  list<Antenato>  $genitori  the capofamiglia first
     * @param  list<RamoPersona>  $rami
     */
    public function __construct(
        public Collection $percorso,
        public ?Famiglia $origine,
        public array $genitori,
        public array $rami,
    ) {}

    /**
     * How a persona entered a famiglia as figlio, e.g. "nata 1980", "accolto 1992", "aggregata" when the date is not known.
     * A figlio nato enters the famiglia at the birth.
     */
    public static function etichettaTappa(Persona $persona, string $posizione, ?string $dataEntrata): string
    {
        $femmina = ! $persona->isMaschio();
        $ruolo = match ($posizione) {
            'FIGLIO NATO' => $femmina ? 'nata' : 'nato',
            'AGGREGATO' => $femmina ? 'aggregata' : 'aggregato',
            default => $femmina ? 'accolta' : 'accolto',
        };
        $data = $dataEntrata ?? ($posizione === 'FIGLIO NATO' ? $persona->data_nascita : null);

        return $data === null ? $ruolo : $ruolo.' '.Date::parse($data)->year;
    }
}
