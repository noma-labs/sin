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
 * or in a whole month, with the round ones until the end of the year.
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

        return view('nomadelfia.ricorrenze.index', [
            'oggi' => $oggi,
            'settimana' => $settimana,
            'mese' => $mese,
            'inizioPeriodo' => $inizioPeriodo,
            'tipi' => TipoRicorrenza::cases(),
            'conteggi' => $nelPeriodo->countBy(fn (Ricorrenza $ricorrenza): string => $ricorrenza->tipo->value),
            'totale' => $nelPeriodo->count(),
            'giorni' => $nelPeriodo->groupBy(fn (Ricorrenza $ricorrenza): string => $ricorrenza->data->toDateString()),
            'giubilei' => $ricorrenze->filter(fn (Ricorrenza $ricorrenza): bool => $ricorrenza->giubileo() !== null && $ricorrenza->data->betweenIncluded($oggi, $fineAnno)),
        ]);
    }
}
