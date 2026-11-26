<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Anagrafe\Persona\Models\Persona;
use App\Photo\DataTransferObjects\Volto;
use App\Photo\Models\Photo;
use Illuminate\Database\Eloquent\Collection;

/**
 * The faces of a persona tagged in the photos of the archive, to choose their avatar: in the photos of the persona
 * (Photo::idsOfPersona), the face regions named as the persona in that photo (photos_people.persona_nome).
 */
final class VoltiPersonaAction
{
    /**
     * The width in pixels of a face that gives a sharp avatar of 256px once cut with its margin.
     */
    public const LATO_CONSIGLIATO = 160;

    /**
     * Ordered from the most recent photo (taken_at), the photos without a date last. The suggested face is the most recent
     * at least LATO_CONSIGLIATO wide, or the largest one when none is.
     *
     * @return list<Volto>
     */
    public function execute(Persona $persona): array
    {
        $nomiPerFoto = [];
        foreach (Photo::idsOfPersona($persona->id, $persona->nomeNelleFoto())->addSelect('persona_nome')->get() as $riga) {
            if (is_string($riga->persona_nome ?? null)) {
                $nomiPerFoto[(int) $riga->photo_id][] = $this->normalizza($riga->persona_nome);
            }
        }

        $volti = [];
        foreach (Photo::query()->findMany(array_keys($nomiPerFoto)) as $photo) {
            $larghezza = $photo->image_width ?? (int) ($photo->region_info->AppliedToDimensions['W'] ?? 0);
            foreach ($photo->region_info->RegionList ?? [] as $indice => $regione) {
                $area = $this->area($regione->Area);
                if (mb_strtolower($regione->Type) !== 'face' || $area === null || ! in_array($this->normalizza($regione->Name), $nomiPerFoto[$photo->id], true)) {
                    continue;
                }
                $volti[] = new Volto($photo->id, $indice, $photo->source_file, $area, (int) round($area['W'] * $larghezza), $photo->taken_at);
            }
        }

        usort($volti, fn (Volto $a, Volto $b): int => $this->ordine($b) <=> $this->ordine($a));

        return $this->segnaConsigliato($volti);
    }

    /**
     * The photos of the persona where no face region carries their name, from the most recent: the box around the
     * persona can be drawn on them by hand.
     *
     * @return Collection<int, Photo>
     */
    public function fotoSenzaVolto(Persona $persona): Collection
    {
        $conVolto = array_map(fn (Volto $volto): int => $volto->photoId, $this->execute($persona));

        $foto = Photo::query()->whereKey($this->idsFoto($persona))->whereKeyNot($conVolto);
        $foto->orderByRaw('taken_at IS NULL')->latest('taken_at')->orderBy('id', 'desc');

        return $foto->get();
    }

    /**
     * The photo when it is one of the persona (Photo::idsOfPersona), null otherwise.
     */
    public function foto(Persona $persona, int $photoId): ?Photo
    {
        return in_array($photoId, $this->idsFoto($persona), true) ? Photo::query()->find($photoId) : null;
    }

    /**
     * The face of the persona in that region of that photo, null when it is not one of theirs.
     */
    public function volto(Persona $persona, int $photoId, int $indice): ?Volto
    {
        foreach ($this->execute($persona) as $volto) {
            if ($volto->photoId === $photoId && $volto->indice === $indice) {
                return $volto;
            }
        }

        return null;
    }

    /**
     * @return list<int> the ids of the photos of the persona
     */
    private function idsFoto(Persona $persona): array
    {
        return array_values(Photo::idsOfPersona($persona->id, $persona->nomeNelleFoto())
            ->distinct()
            ->pluck('photo_id')
            ->map(fn (mixed $id): int => is_numeric($id) ? (int) $id : 0)
            ->all());
    }

    /**
     * The sort key of a face, the greatest first: the photos with a date before the others, then the most recent.
     *
     * @return array{0: int, 1: int, 2: int}
     */
    private function ordine(Volto $volto): array
    {
        return [$volto->data === null ? 0 : 1, $volto->data?->getTimestamp() ?? 0, $volto->photoId];
    }

    /**
     * @param  list<Volto>  $volti
     * @return list<Volto>
     */
    private function segnaConsigliato(array $volti): array
    {
        if ($volti === []) {
            return [];
        }

        $consigliato = null;
        foreach ($volti as $posizione => $volto) {
            if ($volto->latoPx >= self::LATO_CONSIGLIATO) {
                $consigliato = $posizione;
                break;
            }
        }
        if ($consigliato === null) {
            $lati = array_map(fn (Volto $volto): int => $volto->latoPx, $volti);
            $consigliato = (int) array_search(max($lati), $lati, true);
        }

        $volti[$consigliato] = $volti[$consigliato]->conConsigliato(true);

        return array_values($volti);
    }

    /**
     * @param  array<string, mixed>  $area
     * @return array{X: float, Y: float, W: float, H: float}|null null without a size
     */
    private function area(array $area): ?array
    {
        foreach (['X', 'Y', 'W', 'H'] as $chiave) {
            if (! is_numeric($area[$chiave] ?? null)) {
                return null;
            }
        }
        if ((float) $area['W'] <= 0 || (float) $area['H'] <= 0) {
            return null;
        }

        return ['X' => (float) $area['X'], 'Y' => (float) $area['Y'], 'W' => (float) $area['W'], 'H' => (float) $area['H']];
    }

    private function normalizza(string $nome): string
    {
        return (string) preg_replace('/\s+/', ' ', mb_strtolower(mb_trim($nome)));
    }
}
