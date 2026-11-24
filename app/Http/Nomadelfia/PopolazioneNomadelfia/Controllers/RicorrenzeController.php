<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Nomadelfia\PopolazioneNomadelfia\Actions\BuildRicorrenzeAction;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\Ricorrenza;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\TipoRicorrenza;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Date;

/**
 * The anniversaries of the community (matrimoni, compleanni, mamme di vocazione, sacerdozi, decessi) in the next days
 * or in a whole month, with the round ones until the end of the year. The period and the chosen tipi are kept in the query.
 */
#[Middleware('auth')]
final class RicorrenzeController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function index(Request $request, BuildRicorrenzeAction $buildRicorrenze): View
    {
        $oggi = Date::today();
        $fineAnno = $oggi->copy()->endOfYear()->startOfDay();
        $mese = $request->integer('mese');
        $mese = $mese >= 1 && $mese <= 12 ? $mese : null;
        $settimana = $mese === null && $request->query('periodo') === 'settimana';
        $tipiScelti = $this->tipiScelti($request->query('tipi'));

        if ($mese !== null) {
            // the next occurrence of the month: a month already passed this year is the one of the next year
            $inizioPeriodo = $oggi->copy()->setDate($mese < $oggi->month ? $oggi->year + 1 : $oggi->year, $mese, 1);
            $finePeriodo = $inizioPeriodo->copy()->endOfMonth()->startOfDay();
        } else {
            $inizioPeriodo = $oggi;
            $finePeriodo = $oggi->copy()->addDays($settimana ? 6 : 29);
        }

        $ricorrenze = $buildRicorrenze->execute($inizioPeriodo->min($oggi), $finePeriodo->max($fineAnno));
        $nelPeriodo = $ricorrenze->filter(fn (Ricorrenza $ricorrenza): bool => $ricorrenza->data->betweenIncluded($inizioPeriodo, $finePeriodo));
        $mostrate = $tipiScelti === [] ? $nelPeriodo : $nelPeriodo->filter(fn (Ricorrenza $ricorrenza): bool => in_array($ricorrenza->tipo, $tipiScelti, true));
        $queryTipi = $this->queryTipi($tipiScelti);
        $queryPeriodo = match (true) {
            $mese !== null => ['mese' => $mese],
            $settimana => ['periodo' => 'settimana'],
            default => [],
        };

        return view('nomadelfia.ricorrenze.index', [
            'oggi' => $oggi,
            'settimana' => $settimana,
            'mese' => $mese,
            'inizioPeriodo' => $inizioPeriodo,
            'queryTipi' => $queryTipi,
            'filtri' => array_map(fn (TipoRicorrenza $tipo): array => [
                'tipo' => $tipo,
                'scelto' => in_array($tipo, $tipiScelti, true),
                'url' => route('nomadelfia.ricorrenze', [
                    ...$queryPeriodo,
                    ...$this->queryTipi(in_array($tipo, $tipiScelti, true)
                        ? array_filter($tipiScelti, fn (TipoRicorrenza $scelto): bool => $scelto !== $tipo)
                        : [...$tipiScelti, $tipo]),
                ]),
            ], TipoRicorrenza::cases()),
            'conteggi' => $nelPeriodo->countBy(fn (Ricorrenza $ricorrenza): string => $ricorrenza->tipo->value),
            'totale' => $mostrate->count(),
            'giorni' => $mostrate->groupBy(fn (Ricorrenza $ricorrenza): string => $ricorrenza->data->toDateString()),
            'giubilei' => $ricorrenze->filter(fn (Ricorrenza $ricorrenza): bool => $ricorrenza->giubileo() !== null && $ricorrenza->data->betweenIncluded($oggi, $fineAnno)),
        ]);
    }

    /**
     * The tipi chosen in the query as a comma separated list (e.g. `decesso,compleanno`), ignoring the unknown ones.
     *
     * @return list<TipoRicorrenza>
     */
    private function tipiScelti(mixed $tipi): array
    {
        if (! is_string($tipi)) {
            return [];
        }

        $scelti = array_map(fn (string $tipo): ?TipoRicorrenza => TipoRicorrenza::tryFrom(mb_trim($tipo)), explode(',', $tipi));

        return array_values(array_filter(TipoRicorrenza::cases(), fn (TipoRicorrenza $tipo): bool => in_array($tipo, $scelti, true)));
    }

    /**
     * The query that keeps the given tipi, in the order of the cases, or nothing when no tipo is chosen.
     *
     * @param  array<TipoRicorrenza>  $tipi
     * @return array{tipi?: string}
     */
    private function queryTipi(array $tipi): array
    {
        $valori = array_map(fn (TipoRicorrenza $tipo): string => $tipo->value, array_filter(TipoRicorrenza::cases(), fn (TipoRicorrenza $tipo): bool => in_array($tipo, $tipi, true)));

        return $valori === [] ? [] : ['tipi' => implode(',', $valori)];
    }
}
