<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\FamigliaPersona;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Persona\Actions\BuildPersonTimelineAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use App\Photo\Models\Photo;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The profile page of a person: summary on top, timeline of the important events, current situation and family.
 */
#[Middleware('auth')]
final class PersonProfileController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function show(string $id, BuildPersonTimelineAction $buildTimeline): View
    {
        $persona = Persona::query()->findOrFail($id);
        $famiglia = $persona->ultimaFamiglia();
        $nelNucleo = $famiglia?->pivot->stato === '1';
        $posizioneFamiglia = $famiglia?->pivot->posizione_famiglia;
        $inCoppia = in_array($posizioneFamiglia, ['CAPO FAMIGLIA', 'MOGLIE'], true);

        $coniuge = $inCoppia
            ? ($nelNucleo ? $famiglia->componentiAttuali() : $famiglia->componenti())->wherePivot('posizione_famiglia', $posizioneFamiglia === 'CAPO FAMIGLIA' ? 'MOGLIE' : 'CAPO FAMIGLIA')->first()
            : null;
        $figliNelNucleo = $inCoppia ? $famiglia->figliAttuali()->get() : collect();
        $figliFuoriNucleo = $inCoppia ? $famiglia->figli()->wherePivot('stato', '0')->get() : collect();

        /** @var Collection<int, Famiglia&object{pivot: FamigliaPersona}> $percorsoFamiliare */
        $percorsoFamiliare = $persona->percorsoFamiliare()->get();
        $famigliaOrigine = $percorsoFamiliare->first(
            fn ($famiglia): bool => in_array($famiglia->pivot->posizione_famiglia, ['FIGLIO NATO', 'FIGLIO ACCOLTO', 'AGGREGATO'], true)
        );
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
            'statoCivile' => $persona->statoCivile(),
            'statiAttivi' => $persona->statiAttivi()->get()->reject(fn (Stato $stato): bool => $stato->isStatoCivile())->values(),
            'famigliaSenzaConiuge' => $persona->famigliaSenzaConiuge(),
            'gruppoAttuale' => $persona->gruppofamiliareAttuale(),
            'aziende' => $persona->aziendeAttuali()->get(),
            'incarichi' => $persona->incarichiAttuali()->get(),
            'famiglia' => $famiglia,
            'nelNucleo' => $nelNucleo,
            'posizioneFamiglia' => $posizioneFamiglia,
            'coniuge' => $coniuge,
            'figliNelNucleo' => $figliNelNucleo,
            'figliFuoriNucleo' => $figliFuoriNucleo,
            'famigliaOrigine' => $famigliaOrigine,
            'genitori' => $genitori,
            'percorsoFamiliare' => $this->tappeFamiliari($persona, $percorsoFamiliare),
            'famigliaEnrico' => $famigliaEnrico,
            'periodi' => $this->periodiInNomadelfia($persona),
            'timeline' => $buildTimeline->execute($persona),
            'fotoCount' => Photo::idsWherePersonaAppears($persona->id)->count(),
        ]);
    }

    /**
     * The famiglie of the persona from the most recent, each with the role of the persona in it.
     * The date is derived when it can be: the birth for a figlio nato, the data_creazione of the famiglia for a
     * capofamiglia or a moglie, the first entrata in the comunità for the first famiglia of the persona as figlio
     * accolto or aggregato. A figlio accolto or aggregato who changed famiglia has the data_entrata recorded in
     * the famiglia, if known.
     *
     * @param  Collection<int, Famiglia&object{pivot: FamigliaPersona}>  $famiglie  ordered from the first to the last one
     * @return Collection<int, array{famiglia: Famiglia, ruolo: string, tipo: string, nelNucleo: bool, dal: ?Carbon}>
     */
    private function tappeFamiliari(Persona $persona, Collection $famiglie): Collection
    {
        $maschio = $persona->isMaschio();
        $primaFamiglia = $famiglie->first();
        $entrataPrimaAccoglienza = null;
        if (in_array($primaFamiglia?->pivot->posizione_famiglia, ['FIGLIO ACCOLTO', 'AGGREGATO'], true) && $primaFamiglia->pivot->data_entrata === null) {
            $entrata = PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->min('data_entrata');
            $entrataPrimaAccoglienza = is_string($entrata) ? Date::parse($entrata) : null;
        }

        return $famiglie
            ->map(fn ($famiglia): array => [
                'famiglia' => $famiglia,
                'ruolo' => match ($famiglia->pivot->posizione_famiglia) {
                    'CAPO FAMIGLIA' => 'Capofamiglia',
                    'MOGLIE' => 'Moglie',
                    'FIGLIO NATO' => $maschio ? 'Figlio nato' : 'Figlia nata',
                    'FIGLIO ACCOLTO' => $maschio ? 'Figlio accolto' : 'Figlia accolta',
                    'AGGREGATO' => $maschio ? 'Aggregato' : 'Aggregata',
                    default => Str::ucfirst(mb_strtolower($famiglia->pivot->posizione_famiglia)),
                },
                'tipo' => match ($famiglia->pivot->posizione_famiglia) {
                    'CAPO FAMIGLIA', 'MOGLIE' => 'coniuge',
                    'FIGLIO NATO' => 'nato',
                    'FIGLIO ACCOLTO' => 'accolto',
                    default => 'aggregato',
                },
                'nelNucleo' => $famiglia->pivot->stato === '1',
                'dal' => match (true) {
                    $famiglia->pivot->posizione_famiglia === 'FIGLIO NATO' => Date::parse($persona->data_nascita),
                    in_array($famiglia->pivot->posizione_famiglia, ['CAPO FAMIGLIA', 'MOGLIE'], true) => $famiglia->data_creazione ? Date::parse($famiglia->data_creazione) : null,
                    $famiglia->pivot->data_entrata !== null => Date::parse($famiglia->pivot->data_entrata),
                    $famiglia->is($primaFamiglia) => $entrataPrimaAccoglienza,
                    default => null,
                },
            ])
            ->reverse()
            ->values();
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
