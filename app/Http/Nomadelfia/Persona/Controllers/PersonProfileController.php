<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Persona\Actions\BuildPersonTimelineAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

/**
 * The new profile page of a person: summary on top, timeline of the important events, current situation and family.
 * It lives next to the classic page (PersonaPopolazioneController@show) so the users can go back to it.
 */
#[Middleware('auth')]
final class PersonProfileController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function show(string $id, BuildPersonTimelineAction $buildTimeline): View
    {
        $persona = Persona::query()->findOrFail($id);
        $famigliaAttuale = $persona->famigliaAttuale();
        $posizioneFamiglia = $famigliaAttuale?->pivot->posizione_famiglia;
        $inCoppia = in_array($posizioneFamiglia, ['CAPO FAMIGLIA', 'MOGLIE'], true);

        $coniuge = $inCoppia
            ? $famigliaAttuale->componentiAttuali()->wherePivot('posizione_famiglia', $posizioneFamiglia === 'CAPO FAMIGLIA' ? 'MOGLIE' : 'CAPO FAMIGLIA')->first()
            : null;
        $figliNelNucleo = $inCoppia ? $famigliaAttuale->figliAttuali()->get() : collect();
        $figliFuoriNucleo = $inCoppia ? $famigliaAttuale->figli()->wherePivot('stato', '0')->get() : collect();

        $famigliaOrigine = $persona->famiglie()
            ->withPivot('posizione_famiglia')
            ->wherePivotIn('posizione_famiglia', ['FIGLIO NATO', 'FIGLIO ACCOLTO'])
            ->first();
        $genitori = $famigliaOrigine
            ? $famigliaOrigine->componenti()->wherePivotIn('posizione_famiglia', ['CAPO FAMIGLIA', 'MOGLIE'])->get()
            : collect();

        $famigliaEnrico = DB::connection('db_nomadelfia')
            ->table('alfa_enrico_15_feb_23')
            ->select('famiglia')
            ->where('id', $persona->id_alfa_enrico)
            ->first();

        return view('nomadelfia.persone.profile', [
            'persona' => $persona,
            'isPresente' => $persona->isPersonaInterna(),
            'posizioneAttuale' => $persona->posizioneAttuale(),
            'statoAttuale' => $persona->statoAttuale(),
            'gruppoAttuale' => $persona->gruppofamiliareAttuale(),
            'aziende' => $persona->aziendeAttuali()->get(),
            'incarichi' => $persona->incarichiAttuali()->get(),
            'famigliaAttuale' => $famigliaAttuale,
            'posizioneFamiglia' => $posizioneFamiglia,
            'coniuge' => $coniuge,
            'figliNelNucleo' => $figliNelNucleo,
            'figliFuoriNucleo' => $figliFuoriNucleo,
            'famigliaOrigine' => $famigliaOrigine,
            'genitori' => $genitori,
            'famigliaEnrico' => $famigliaEnrico,
            'periodi' => $this->periodiInNomadelfia($persona),
            'timeline' => $buildTimeline->execute($persona),
        ]);
    }

    /**
     * The periods spent in Nomadelfia, positioned as percentages of the life of the person (from birth to today or death).
     *
     * @return Collection<int, array{entrata: Carbon, uscita: ?Carbon, left: float, width: float}>
     */
    private function periodiInNomadelfia(Persona $persona): Collection
    {
        $nascita = Date::parse($persona->data_nascita);
        $fine = $persona->data_decesso ? Date::parse($persona->data_decesso) : Date::now();
        $vita = max(1, $nascita->diffInDays($fine));

        return PopolazioneNomadelfia::query()
            ->where('persona_id', $persona->id)
            ->orderBy('data_entrata')
            ->get()
            ->map(function ($periodo) use ($nascita, $fine, $vita): array {
                $entrata = Date::parse($periodo->data_entrata);
                $uscita = $periodo->data_uscita ? Date::parse($periodo->data_uscita) : null;

                return [
                    'entrata' => $entrata,
                    'uscita' => $uscita,
                    'left' => round(max(0, $nascita->diffInDays($entrata)) / $vita * 100, 2),
                    'width' => round(max(0, $entrata->diffInDays($uscita ?? $fine)) / $vita * 100, 2),
                ];
            });
    }
}
