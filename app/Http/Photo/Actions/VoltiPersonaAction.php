<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Anagrafe\Persona\Models\Persona;
use App\Photo\DataTransferObjects\Volto;
use App\Photo\Models\Photo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

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
     * How long the ordered faces of a persona stay in the cache, fresh and then stale while they are read again after the
     * response: a persona tagged in thousands of photos takes more than a second to read.
     */
    public const CACHE_FRESCA = 600;

    public const CACHE_SCADUTA = 86400;

    /**
     * The faces large enough for a sharp avatar (LATO_CONSIGLIATO) first, then the smaller ones; each group from the most
     * recent photo (taken_at), the photos without a date last. The suggested face is the first large enough, or the
     * largest one when none is. Cached (CACHE_FRESCA): a new tag can take that long to show.
     *
     * @return list<Volto>
     */
    public function execute(Persona $persona): array
    {
        $inCache = Cache::flexible("avatar.volti.{$persona->id}", [self::CACHE_FRESCA, self::CACHE_SCADUTA], function () use ($persona): array {
            $volti = $this->voltiNelleFoto($persona, null);
            usort($volti, fn (Volto $a, Volto $b): int => $this->ordine($b) <=> $this->ordine($a));

            return $this->segnaConsigliato($volti);
        });

        // the cache gives back mixed: keep only the faces
        return array_values(array_filter(is_array($inCache) ? $inCache : [], fn (mixed $volto): bool => $volto instanceof Volto));
    }

    /**
     * The photos of the persona where no face region carries their name, from the most recent: the box around the
     * persona can be drawn on them by hand.
     *
     * @param  list<Volto>  $volti  the faces of the persona (execute)
     * @return Builder<Photo>
     */
    public function fotoSenzaVolto(Persona $persona, array $volti): Builder
    {
        $conVolto = array_values(array_unique(array_map(fn (Volto $volto): int => $volto->photoId, $volti)));

        $foto = Photo::query()->whereKey(Photo::idsOfPersona($persona->id, $persona->nomeNelleFoto())->distinct()->pluck('photo_id')->all());
        $foto->whereKeyNot($conVolto)->orderByRaw('taken_at IS NULL')->latest('taken_at')->orderBy('id', 'desc');

        return $foto;
    }

    /**
     * The photo when it is one of the persona (Photo::idsOfPersona), null otherwise.
     */
    public function foto(Persona $persona, int $photoId): ?Photo
    {
        $diPersona = Photo::idsOfPersona($persona->id, $persona->nomeNelleFoto())->where('photo_id', $photoId)->exists();

        return $diPersona ? Photo::query()->find($photoId) : null;
    }

    /**
     * The face of the persona in that region of that photo, null when it is not one of theirs: it reads only that photo.
     */
    public function volto(Persona $persona, int $photoId, int $indice): ?Volto
    {
        foreach ($this->voltiNelleFoto($persona, $photoId) as $volto) {
            if ($volto->indice === $indice) {
                return $volto;
            }
        }

        return null;
    }

    /**
     * The face regions named as the persona in their photos, or only in the photo $photoId.
     *
     * @return list<Volto>
     */
    private function voltiNelleFoto(Persona $persona, ?int $photoId): array
    {
        $righe = Photo::idsOfPersona($persona->id, $persona->nomeNelleFoto())
            ->addSelect('persona_nome')
            ->when($photoId !== null, fn ($query) => $query->where('photo_id', $photoId))
            ->get();
        $nomiPerFoto = [];
        foreach ($righe as $riga) {
            if (is_string($riga->persona_nome ?? null)) {
                $nomiPerFoto[(int) $riga->photo_id][] = $this->normalizza($riga->persona_nome);
            }
        }

        $volti = [];
        $foto = Photo::query()->findMany(array_keys($nomiPerFoto), ['id', 'source_file', 'image_width', 'region_info', 'taken_at']);
        foreach ($foto as $photo) {
            $larghezza = $photo->image_width ?? (int) ($photo->region_info->AppliedToDimensions['W'] ?? 0);
            foreach ($photo->region_info->RegionList ?? [] as $indice => $regione) {
                $area = $this->area($regione->Area);
                if (mb_strtolower($regione->Type) !== 'face' || $area === null || ! in_array($this->normalizza($regione->Name), $nomiPerFoto[$photo->id], true)) {
                    continue;
                }
                $volti[] = new Volto($photo->id, $indice, $photo->source_file, $area, (int) round($area['W'] * $larghezza), $photo->taken_at);
            }
        }

        return $volti;
    }

    /**
     * The sort key of a face, the greatest first: the faces large enough for the avatar before the others, then the
     * photos with a date before the ones without, then the most recent.
     *
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    private function ordine(Volto $volto): array
    {
        return [$volto->latoPx >= self::LATO_CONSIGLIATO ? 1 : 0, $volto->data === null ? 0 : 1, $volto->data?->getTimestamp() ?? 0, $volto->photoId];
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
