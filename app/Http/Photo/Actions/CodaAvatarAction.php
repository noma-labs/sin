<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * The persone who are in the photos of the archive, to choose their avatar one after the other: the queue of the ones
 * still without an avatar and the ones that already have it. The photos of a persona are counted as
 * Photo::idsOfPersona finds them.
 */
final class CodaAvatarAction
{
    /**
     * The persone without an avatar in the order of the queue, the ones with more photos first.
     *
     * @return Builder<Persona>
     */
    public function daFare(): Builder
    {
        $coda = $this->personeNelleFoto();
        $coda->whereNull('persone.avatar_path')
            ->orderBy('foto_persona.numero_foto', 'desc')
            ->orderBy('persone.id');

        return $coda;
    }

    /**
     * The persone with an avatar, the last saved first; the avatars saved before avatar_aggiornato_il was recorded
     * come last, by cognome and nome.
     *
     * @return Builder<Persona>
     */
    public function fatte(): Builder
    {
        $fatte = $this->personeNelleFoto();
        $fatte->whereNotNull('persone.avatar_path')
            ->orderByRaw('persone.avatar_aggiornato_il IS NULL')
            ->orderBy('persone.avatar_aggiornato_il', 'desc')
            ->orderBy('persone.cognome')
            ->orderBy('persone.nome');

        return $fatte;
    }

    /**
     * The id of the persona after $persona in the queue, null when $persona is the last one or not in the queue.
     */
    public function prossima(Persona $persona): ?int
    {
        $ids = $this->daFare()->get()->modelKeys();
        $posizione = array_search($persona->id, $ids, true);
        $prossima = $posizione === false ? null : ($ids[$posizione + 1] ?? null);

        return $prossima === null ? null : (int) $prossima;
    }

    /**
     * The persone in the photos, each with numero_foto, the number of their photos.
     *
     * @return Builder<Persona>
     */
    private function personeNelleFoto(): Builder
    {
        $persone = Persona::query();
        $persone->select('persone.*', 'foto_persona.numero_foto')
            ->joinSub($this->numeroFotoPerPersona(), 'foto_persona', 'foto_persona.persona_id', '=', 'persone.id');

        return $persone;
    }

    /**
     * persona_id and numero_foto: the photos where the persona is recognised (photos_people.persona_id) and the ones
     * where a name not yet recognised is their FOTO of the file ALFA (Persona::nomeNelleFoto).
     */
    private function numeroFotoPerPersona(): QueryBuilder
    {
        $riconosciute = DB::connection('db_nomadelfia')->table('db_foto.photos_people')
            ->select('persona_id', 'photo_id')
            ->whereNotNull('persona_id');

        $perNome = DB::connection('db_nomadelfia')->table('persone as p')
            ->select('p.id as persona_id', 'pp.photo_id')
            ->join('alfa_enrico_15_feb_23 as e', 'e.id', '=', 'p.id_alfa_enrico')
            ->join('db_foto.photos_people as pp', 'pp.persona_nome', '=', 'e.FOTO')
            ->whereNull('pp.persona_id');

        return DB::connection('db_nomadelfia')->query()
            ->fromSub($riconosciute->union($perNome), 'foto')
            ->select('persona_id')
            ->selectRaw('COUNT(DISTINCT photo_id) AS numero_foto')
            ->groupBy('persona_id');
    }
}
