<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * The persone who are in the photos of the archive, to choose their avatar one after the other: the queue of the ones
 * still without an avatar and the ones that already have it. The photos of a persona are counted as
 * Photo::idsOfPersona finds them.
 *
 * Counting the photos of every persona reads all of photos_people (seconds in production), so the counts are cached:
 * fresh for CACHE_FRESCA seconds, then served stale while they are counted again after the response, up to
 * CACHE_SCADUTA seconds. A new tag can take that long to reach the queue; the avatars are always read live.
 */
final class CodaAvatarAction
{
    public const CACHE_CHIAVE = 'avatar.coda.numero-foto-per-persona';

    public const CACHE_FRESCA = 600;

    public const CACHE_SCADUTA = 86400;

    /**
     * The number of photos of each persona in the photos (cached).
     *
     * @return array<int, int> persona_id => numero foto
     */
    public function numeroFotoPerPersona(): array
    {
        $inCache = Cache::flexible(self::CACHE_CHIAVE, [self::CACHE_FRESCA, self::CACHE_SCADUTA], fn (): array => $this->contaFoto());

        // the cache gives back mixed: keep only persona_id => numero foto
        $numeroFoto = [];
        foreach (is_array($inCache) ? $inCache : [] as $personaId => $numero) {
            if (is_int($personaId) && is_int($numero)) {
                $numeroFoto[$personaId] = $numero;
            }
        }

        return $numeroFoto;
    }

    /**
     * The ids of the persone without an avatar in the order of the queue: the ones with more photos first, then by id.
     *
     * @return list<int>
     */
    public function idsDaFare(): array
    {
        $numeroFoto = $this->numeroFotoPerPersona();
        $ids = Persona::query()
            ->whereKey(array_keys($numeroFoto))
            ->whereNull('avatar_path')
            ->pluck('id')
            ->map(fn (mixed $id): int => is_numeric($id) ? (int) $id : 0)
            ->all();
        usort($ids, fn (int $a, int $b): int => [$numeroFoto[$b], $a] <=> [$numeroFoto[$a], $b]);

        return $ids;
    }

    /**
     * A page of the queue, each persona with numero_foto, the number of their photos.
     *
     * @return LengthAwarePaginator<int, Persona>
     */
    public function daFare(int $perPagina): LengthAwarePaginator
    {
        $ids = $this->idsDaFare();
        $pagina = Paginator::resolveCurrentPage();
        $idsPagina = array_slice($ids, ($pagina - 1) * $perPagina, $perPagina);

        $numeroFoto = $this->numeroFotoPerPersona();
        $persone = Persona::query()->findMany($idsPagina)->keyBy('id');
        $inPagina = [];
        foreach ($idsPagina as $id) {
            $persona = $persone->get($id);
            if ($persona instanceof Persona) {
                $persona->setAttribute('numero_foto', $numeroFoto[$id]);
                $inPagina[] = $persona;
            }
        }

        return new LengthAwarePaginator($inPagina, count($ids), $perPagina, $pagina, ['path' => Paginator::resolveCurrentPath()]);
    }

    /**
     * The persone of the photos with an avatar, the last saved first; the avatars saved before avatar_aggiornato_il was
     * recorded come last, by cognome and nome.
     *
     * @return Builder<Persona>
     */
    public function fatte(): Builder
    {
        $fatte = Persona::query();
        $fatte->whereKey(array_keys($this->numeroFotoPerPersona()))
            ->whereNotNull('avatar_path')
            ->orderByRaw('avatar_aggiornato_il IS NULL')
            ->orderBy('avatar_aggiornato_il', 'desc')
            ->orderBy('cognome')
            ->orderBy('nome');

        return $fatte;
    }

    /**
     * The id of the persona that comes after $persona in the queue, also when $persona has just left it by getting an
     * avatar: the next one by the order of the queue (more photos, then the id). Null after the last one or when
     * $persona is not in the photos.
     */
    public function prossima(Persona $persona): ?int
    {
        $numeroFoto = $this->numeroFotoPerPersona();
        if (! isset($numeroFoto[$persona->id])) {
            return null;
        }

        $posizione = [-$numeroFoto[$persona->id], $persona->id];
        foreach ($this->idsDaFare() as $id) {
            if ([-$numeroFoto[$id], $id] > $posizione) {
                return $id;
            }
        }

        return null;
    }

    /**
     * persona_id => numero foto: the photos where the persona is recognised (photos_people.persona_id) and the ones
     * where a name not yet recognised is their FOTO of the file ALFA (Persona::nomeNelleFoto).
     *
     * @return array<int, int>
     */
    private function contaFoto(): array
    {
        $riconosciute = DB::connection('db_nomadelfia')->table('db_foto.photos_people')
            ->select('persona_id', 'photo_id')
            ->whereNotNull('persona_id');

        $perNome = DB::connection('db_nomadelfia')->table('persone as p')
            ->select('p.id as persona_id', 'pp.photo_id')
            ->join('alfa_enrico_15_feb_23 as e', 'e.id', '=', 'p.id_alfa_enrico')
            ->join('db_foto.photos_people as pp', 'pp.persona_nome', '=', 'e.FOTO')
            ->whereNull('pp.persona_id');

        // UNION ALL: COUNT(DISTINCT) already skips a photo found both ways, without sorting all the rows for the UNION
        $righe = DB::connection('db_nomadelfia')->query()
            ->fromSub($riconosciute->unionAll($perNome), 'foto')
            ->select('persona_id')
            ->selectRaw('COUNT(DISTINCT photo_id) AS numero_foto')
            ->groupBy('persona_id')
            ->get();

        $numeroFoto = [];
        foreach ($righe as $riga) {
            if (is_numeric($riga->persona_id ?? null) && is_numeric($riga->numero_foto ?? null)) {
                $numeroFoto[(int) $riga->persona_id] = (int) $riga->numero_foto;
            }
        }

        return $numeroFoto;
    }
}
