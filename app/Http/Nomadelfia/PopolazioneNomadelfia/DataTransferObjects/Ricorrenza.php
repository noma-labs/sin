<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

/**
 * An anniversary of an event of the community (matrimonio, entrata, mamma di vocazione, sacerdozio, decesso) falling on a day.
 */
final readonly class Ricorrenza
{
    /**
     * @param  Collection<int, Persona>  $persone  the people the anniversary belongs to
     */
    public function __construct(
        public Carbon $data,
        public TipoRicorrenza $tipo,
        public Carbon $origine,
        public int $anni,
        public Collection $persone,
    ) {}

    public function titolo(): string
    {
        $anni = $this->anni === 1 ? '1 anno' : $this->anni.' anni';

        return match ($this->tipo) {
            TipoRicorrenza::Matrimonio => $anni.' di matrimonio',
            TipoRicorrenza::Entrata => $anni.' in Nomadelfia',
            TipoRicorrenza::MammaDiVocazione => $anni.' come mamma di vocazione',
            TipoRicorrenza::Sacerdozio => $anni.' di sacerdozio',
            TipoRicorrenza::Decesso => $this->anni.'° anniversario della morte',
        };
    }

    public function dettaglio(): string
    {
        $origine = $this->origine->translatedFormat('j F Y');
        $persona = $this->persone->first();

        return match ($this->tipo) {
            TipoRicorrenza::Matrimonio => 'Sposati il '.$origine,
            TipoRicorrenza::Entrata => match (true) {
                $this->persone->count() > 1 => 'Entrati il '.$origine,
                $persona?->isMaschio() => 'Entrato il '.$origine,
                default => 'Entrata il '.$origine,
            },
            TipoRicorrenza::MammaDiVocazione => 'Mamma di vocazione dal '.$origine,
            TipoRicorrenza::Sacerdozio => 'Ordinato sacerdote il '.$origine,
            TipoRicorrenza::Decesso => ($persona?->isMaschio() ? 'Morto' : 'Morta').' il '.$origine
                .($persona?->data_nascita ? ', a '.(int) Date::parse($persona->data_nascita)->diffInYears($this->origine).' anni' : ''),
        };
    }

    /**
     * The name of a round anniversary (25, 50, 60, 70, 75... years), null for the others and for a decesso.
     */
    public function giubileo(): ?string
    {
        if ($this->tipo === TipoRicorrenza::Decesso || $this->anni < 25 || ($this->anni % 25 !== 0 && $this->anni % 10 !== 0)) {
            return null;
        }

        $prefisso = $this->tipo === TipoRicorrenza::Matrimonio ? 'Nozze' : 'Giubileo';

        return match ($this->anni) {
            25 => $prefisso.' d’argento',
            50 => $prefisso.' d’oro',
            60 => $prefisso.' di diamante',
            default => 'Giubileo dei '.$this->anni.' anni',
        };
    }
}
