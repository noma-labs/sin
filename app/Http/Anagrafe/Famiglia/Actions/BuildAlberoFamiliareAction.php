<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Actions;

use App\Anagrafe\Famiglia\DataTransferObjects\AlberoFamiliare;
use App\Anagrafe\Famiglia\DataTransferObjects\Antenato;
use App\Anagrafe\Famiglia\DataTransferObjects\RamoFamiglia;
use App\Anagrafe\Famiglia\DataTransferObjects\RamoPersona;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\FamigliaPersona;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Support\Collection;

/**
 * Builds the albero familiare of a persona on four generations: the nonni, the genitori (capofamiglia and moglie of the
 * famiglia d'origine), the persona and the fratelli with the famiglie each of them formed (coniuge and figli).
 * The famiglia d'origine is the chosen one among the famiglie of the persona as figlio, or the last one: for a figlio
 * accolto in more famiglie the most recent. A persona without a famiglia d'origine, entered in Nomadelfia as an adult,
 * is the top of the albero.
 */
final class BuildAlberoFamiliareAction
{
    private const POSIZIONI_GENITORE = ['CAPO FAMIGLIA', 'MOGLIE'];

    public function execute(Persona $persona, ?int $origineScelta = null): AlberoFamiliare
    {
        $percorso = $persona->famiglieComeFiglio()->get();
        $origine = $percorso->first(fn ($famiglia): bool => $famiglia->id === $origineScelta) ?? $percorso->last();

        if ($origine === null) {
            return new AlberoFamiliare($percorso, null, [], [$this->ramoPersona($persona, null)]);
        }

        $genitori = array_values($this->genitori($origine)
            ->map(fn ($genitore): Antenato => new Antenato($genitore, $this->genitoriDi($genitore)))
            ->all());

        $rami = array_values($origine->figli()->with('famiglieFormate')->get()
            ->map(fn ($figlio): RamoPersona => $this->ramoPersona($figlio, $figlio->pivot->posizione_famiglia))
            ->all());

        return new AlberoFamiliare($percorso, $origine, $genitori, $rami);
    }

    /**
     * The last famiglia of the persona as figlio, the same default as the famiglia d'origine of the albero.
     */
    private function origine(Persona $persona): ?Famiglia
    {
        return $persona->famiglieComeFiglio()->get()->last();
    }

    /**
     * @return Collection<int, Persona> the capofamiglia first
     */
    private function genitori(Famiglia $famiglia): Collection
    {
        return $famiglia->componenti()
            ->wherePivotIn('posizione_famiglia', self::POSIZIONI_GENITORE)
            ->get()
            ->sortBy(fn ($genitore): int => $genitore->pivot->posizione_famiglia === 'CAPO FAMIGLIA' ? 0 : 1)
            ->values();
    }

    /**
     * @return Collection<int, Persona>|null null when the persona has no famiglia d'origine
     */
    private function genitoriDi(Persona $persona): ?Collection
    {
        $origine = $this->origine($persona);

        return $origine === null ? null : $this->genitori($origine);
    }

    private function ramoPersona(Persona $persona, ?string $posizione): RamoPersona
    {
        $famiglie = array_values($persona->famiglieFormate
            ->map(fn ($famiglia): RamoFamiglia => $this->ramoFamiglia($famiglia, $persona))
            ->all());

        return new RamoPersona($persona, $posizione, $famiglie);
    }

    /**
     * @param  Famiglia&object{pivot: FamigliaPersona}  $famiglia  with the posizione of the persona who formed it
     */
    private function ramoFamiglia(Famiglia $famiglia, Persona $persona): RamoFamiglia
    {
        $coniuge = $this->genitori($famiglia)->first(fn ($genitore): bool => $genitore->id !== $persona->id);

        return new RamoFamiglia(
            $famiglia,
            $famiglia->pivot->posizione_famiglia,
            $coniuge,
            $famiglia->figli()->with(['famiglieFormate', 'famiglieComeFiglio'])->get(),
        );
    }
}
