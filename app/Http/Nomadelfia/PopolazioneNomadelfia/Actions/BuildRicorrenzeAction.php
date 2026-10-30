<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\Ricorrenza;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\TipoRicorrenza;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Builds the anniversaries remembered by the community in a period:
 * the matrimonio of the coniugi in Nomadelfia, the entrata of the people present (not the ones born in Nomadelfia),
 * the start of a mamma di vocazione, the ordination of the sacerdoti present and the death of the people who lived in Nomadelfia.
 */
final class BuildRicorrenzeAction
{
    /**
     * @return Collection<int, Ricorrenza> the anniversaries between the two days (included), ordered by day
     */
    public function execute(Carbon $dal, Carbon $al): Collection
    {
        $dal = $dal->copy()->startOfDay();
        $al = $al->copy()->startOfDay();

        $candidati = [];
        foreach ($this->eventi() as $evento) {
            $origine = Date::parse($evento['origine'])->startOfDay();
            $data = $this->anniversario($origine, $dal, $al);
            if ($data instanceof Carbon && $data->year > $origine->year) {
                $candidati[] = ['tipo' => $evento['tipo'], 'origine' => $origine, 'data' => $data, 'persone' => $evento['persone']];
            }
        }

        $persone = Persona::query()->findMany(array_merge(...array_column($candidati, 'persone')))->keyBy('id');
        $ordineTipi = array_flip(array_map(fn (TipoRicorrenza $tipo): string => $tipo->value, TipoRicorrenza::cases()));

        $ricorrenze = [];
        foreach ($candidati as $candidato) {
            $ricorrenze[] = new Ricorrenza(
                $candidato['data'],
                $candidato['tipo'],
                $candidato['origine'],
                $candidato['data']->year - $candidato['origine']->year,
                $persone->only($candidato['persone'])->sortBy(fn (Persona $persona): int|false => array_search($persona->id, $candidato['persone'], true))->values()->toBase(),
            );
        }

        return collect($ricorrenze)
            ->sort(fn (Ricorrenza $a, Ricorrenza $b): int => [$a->data->getTimestamp(), $ordineTipi[$a->tipo->value], $a->persone->first()?->nominativo]
                <=> [$b->data->getTimestamp(), $ordineTipi[$b->tipo->value], $b->persone->first()?->nominativo])
            ->values();
    }

    /**
     * The events whose anniversary is remembered, with the day they happened and the people involved.
     *
     * @return list<array{tipo: TipoRicorrenza, origine: string, persone: list<int>}>
     */
    private function eventi(): array
    {
        $eventi = [];
        $db = DB::connection('db_nomadelfia');
        $presenti = Persona::queryIdsInterni()->get()->map(fn (stdClass $presente): int => (int) $presente->persona_id);

        // the people of a famiglia who entered on the same day are remembered together
        $entrate = $db->table('v_popolazione_attuale')
            ->whereColumn('data_entrata', '!=', 'data_nascita')
            ->orderBy('data_nascita')
            ->get(['id', 'data_entrata', 'famiglia']);
        $entratiInsieme = [];
        foreach ($entrate as $persona) {
            $entratiInsieme[$persona->data_entrata.'|'.($persona->famiglia ?? 'persona-'.$persona->id)][] = $persona;
        }
        foreach ($entratiInsieme as $entrati) {
            $eventi[] = ['tipo' => TipoRicorrenza::Entrata, 'origine' => (string) $entrati[0]->data_entrata, 'persone' => array_map(fn (stdClass $persona): int => (int) $persona->id, $entrati)];
        }

        $separati = $db->table('persone_stati')
            ->join('stati', 'stati.id', '=', 'persone_stati.stato_id')
            ->where('stati.stato', 'SEP')
            ->where('persone_stati.stato', '1')
            ->get(['persone_stati.persona_id'])
            ->map(fn (stdClass $separato): int => (int) $separato->persona_id);

        foreach ($this->famiglie([TipoFamiglia::Coniugi], ['CAPO FAMIGLIA', 'MOGLIE']) as $coniugi) {
            $ids = array_map(fn (stdClass $coniuge): int => (int) $coniuge->persona_id, $coniugi);
            $vivi = array_filter($coniugi, fn (stdClass $coniuge): bool => $coniuge->data_decesso === null);
            if (count($coniugi) === 2 && count($vivi) === 2 && $presenti->intersect($ids)->isNotEmpty() && $separati->intersect($ids)->isEmpty()) {
                $eventi[] = ['tipo' => TipoRicorrenza::Matrimonio, 'origine' => (string) $coniugi[0]->data_creazione, 'persone' => $ids];
            }
        }

        foreach ($this->famiglie([TipoFamiglia::MammaDiVocazione], ['CAPO FAMIGLIA']) as $mamme) {
            $ids = array_map(fn (stdClass $mamma): int => (int) $mamma->persona_id, $mamme);
            if ($presenti->intersect($ids)->isNotEmpty()) {
                $eventi[] = ['tipo' => TipoRicorrenza::MammaDiVocazione, 'origine' => (string) $mamme[0]->data_creazione, 'persone' => $ids];
            }
        }

        $sacerdoti = $db->table('persone_stati')
            ->join('stati', 'stati.id', '=', 'persone_stati.stato_id')
            ->where('stati.stato', 'SAC')
            ->whereIn('persone_stati.persona_id', Persona::queryIdsInterni())
            ->whereNotNull('persone_stati.data_inizio')
            ->get(['persone_stati.persona_id', 'persone_stati.data_inizio']);
        foreach ($sacerdoti as $sacerdote) {
            $eventi[] = ['tipo' => TipoRicorrenza::Sacerdozio, 'origine' => (string) $sacerdote->data_inizio, 'persone' => [(int) $sacerdote->persona_id]];
        }

        // the people who lived in Nomadelfia
        $defunti = $db->table('persone')
            ->whereNotNull('data_decesso')
            ->whereNull('deleted_at')
            ->whereIn('id', $db->table('popolazione')->select('persona_id'))
            ->get(['id', 'data_decesso']);
        foreach ($defunti as $defunto) {
            $eventi[] = ['tipo' => TipoRicorrenza::Decesso, 'origine' => (string) $defunto->data_decesso, 'persone' => [(int) $defunto->id]];
        }

        return $eventi;
    }

    /**
     * The famiglie of the tipi with a data_creazione, each as the list of its componenti in the posizioni, the capofamiglia before the moglie.
     *
     * @param  list<TipoFamiglia>  $tipi
     * @param  list<string>  $posizioni
     * @return array<int|string, non-empty-list<stdClass>>
     */
    private function famiglie(array $tipi, array $posizioni): array
    {
        $componenti = DB::connection('db_nomadelfia')
            ->table('famiglie')
            ->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'famiglie.id')
            ->join('persone', 'persone.id', '=', 'famiglie_persone.persona_id')
            ->whereNotNull('famiglie.data_creazione')
            ->whereNull('persone.deleted_at')
            ->whereIn('famiglie_persone.posizione_famiglia', $posizioni)
            ->where(function (Builder $query) use ($tipi): void {
                $query->whereIn('famiglie.tipo', array_map(fn (TipoFamiglia $tipo): string => $tipo->value, $tipi));
                if (in_array(TipoFamiglia::Coniugi, $tipi, true)) {
                    $query->orWhereNull('famiglie.tipo');
                }
            })
            ->orderBy('famiglie_persone.posizione_famiglia')
            ->get(['famiglie.id', 'famiglie.data_creazione', 'persone.id as persona_id', 'persone.data_decesso']);

        $famiglie = [];
        foreach ($componenti as $componente) {
            $famiglie[$componente->id][] = $componente;
        }

        return $famiglie;
    }

    /**
     * The day between $dal and $al on which the event falls again, if any. An event of 29 February falls on 28 February in the other years.
     */
    private function anniversario(Carbon $origine, Carbon $dal, Carbon $al): ?Carbon
    {
        foreach ([$dal->year, $dal->year + 1] as $anno) {
            $data = $dal->copy()->setDate($anno, $origine->month, 1);
            $data->day(min($origine->day, $data->daysInMonth));
            if ($data->betweenIncluded($dal, $al)) {
                return $data;
            }
        }

        return null;
    }
}
