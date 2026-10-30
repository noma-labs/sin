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
 * The anniversaries of the community in the next days (matrimoni, entrate, mamme di vocazione, sacerdozi, decessi),
 * with the round ones until the end of the year.
 */
#[Middleware('auth')]
final class RicorrenzeController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function index(Request $request, BuildRicorrenzeAction $buildRicorrenze): View
    {
        $settimana = $request->query('periodo') === 'settimana';
        $oggi = Date::today();
        $finePeriodo = $oggi->copy()->addDays($settimana ? 6 : 29);
        $fineAnno = $oggi->copy()->endOfYear()->startOfDay();

        $ricorrenze = $buildRicorrenze->execute($oggi, $finePeriodo->max($fineAnno));
        $nelPeriodo = $ricorrenze->filter(fn (Ricorrenza $ricorrenza): bool => $ricorrenza->data->lessThanOrEqualTo($finePeriodo));

        return view('nomadelfia.ricorrenze.index', [
            'oggi' => $oggi,
            'settimana' => $settimana,
            'tipi' => TipoRicorrenza::cases(),
            'conteggi' => $nelPeriodo->countBy(fn (Ricorrenza $ricorrenza): string => $ricorrenza->tipo->value),
            'totale' => $nelPeriodo->count(),
            'giorni' => $nelPeriodo->groupBy(fn (Ricorrenza $ricorrenza): string => $ricorrenza->data->toDateString()),
            'giubilei' => $ricorrenze->filter(fn (Ricorrenza $ricorrenza): bool => $ricorrenza->giubileo() !== null && $ricorrenza->data->lessThanOrEqualTo($fineAnno)),
        ]);
    }
}
