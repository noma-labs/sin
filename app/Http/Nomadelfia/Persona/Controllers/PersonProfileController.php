<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Incarico\Models\Incarico;
use App\Nomadelfia\Persona\Actions\BuildPersonTimelineAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EliminaDataUscitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ModificaDataEntrataAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ModificaDataUscitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

/**
 * The profile page of a person: summary on top with the current situation, timeline of the important events and family.
 * The entrate and uscite in the timeline can be corrected, showing before saving what moves with each date and what
 * is reopened when the last data di uscita is removed.
 */
#[Middleware('auth')]
final class PersonProfileController
{
    /**
     * The posizioni that can be given to the persona from the profile; figlio comes with the famiglia.
     *
     * @var list<string>
     */
    private const POSIZIONI_COMUNITA = ['POST', 'EFFE', 'OSPP', 'DADE'];

    /**
     * The parentele show one famiglia d'origine and one unione, both chosen in the query: the famiglia d'origine among
     * the famiglie of the persona as figlio (e.g. the famiglia of the birth and the one of the mamma di vocazione), the
     * last one by default as in the albero; the unione among the famiglie formed by the persona (e.g. a nomadelfa mamma
     * who married afterwards), the famiglia attuale by default.
     */
    #[Middleware('can:popolazione.persona.visualizza')]
    public function show(
        Request $request,
        string $id,
        BuildPersonTimelineAction $buildTimeline,
        ModificaDataEntrataAction $modificaDataEntrata,
        ModificaDataUscitaAction $modificaDataUscita,
        EliminaDataUscitaAction $eliminaDataUscita,
    ): View {
        $persona = Persona::query()->findOrFail($id);
        $famiglia = $persona->ultimaFamiglia();
        $nelNucleo = $famiglia?->pivot->stato === '1';
        $posizioneFamiglia = $famiglia?->pivot->posizione_famiglia;

        $unioni = $persona->famiglieFormate;
        $unione = $unioni->first(fn ($candidata): bool => $candidata->id === $request->integer('unione'))
            ?? $unioni->first(fn ($candidata): bool => $candidata->id === $famiglia?->id)
            ?? $unioni->last();
        $posizioneUnione = $unione?->pivot->posizione_famiglia;
        $unioneNelNucleo = $unione?->pivot->stato === '1';

        $coniuge = $unione
            ? ($unioneNelNucleo ? $unione->componentiAttuali() : $unione->componenti())->wherePivot('posizione_famiglia', $posizioneUnione === 'CAPO FAMIGLIA' ? 'MOGLIE' : 'CAPO FAMIGLIA')->first()
            : null;
        $figliNelNucleo = $unione ? $unione->figliAttuali()->get() : collect();
        $figliFuoriNucleo = $unione ? $unione->figli()->wherePivot('stato', '0')->get() : collect();

        $origini = $persona->famiglieComeFiglio()->get();
        $famigliaOrigine = $origini->first(fn ($origine): bool => $origine->id === $request->integer('origine')) ?? $origini->last();
        $genitori = $famigliaOrigine
            ? $famigliaOrigine->componenti()->wherePivotIn('posizione_famiglia', ['CAPO FAMIGLIA', 'MOGLIE'])->get()
            : collect();

        $aziende = $persona->aziendeAttuali()->get();
        $incarichi = $persona->incarichiAttuali()->get();

        $periodiPopolazione = PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->get()->sortByDesc('data_entrata')->values();
        $dateCollegate = $periodiPopolazione->mapWithKeys(fn (PopolazioneNomadelfia $periodo): array => [$periodo->data_entrata => [
            'entrata' => $modificaDataEntrata->collegate($persona, $periodo->data_entrata),
            'uscita' => $periodo->data_uscita === null ? collect() : $modificaDataUscita->collegate($persona, $periodo->data_uscita),
        ]]);
        $ultimoPeriodo = $periodiPopolazione->first();
        $uscitaEliminabile = $ultimoPeriodo !== null && $ultimoPeriodo->data_uscita !== null && $eliminaDataUscita->motivoRifiuto($persona, $ultimoPeriodo) === null;

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
            'statiFamiliari' => Stato::query()
                ->whereIn('stato', Stato::$mapNamesToDB)
                ->unless($persona->isMaschio(), fn ($query) => $query->where('stato', '!=', Stato::$mapNamesToDB['sacerdote']))
                ->orderBy('nome')
                ->get(),
            'statiFamiliariAttivi' => $persona->statiAttivi()->pluck('stati.id'),
            'famigliaSenzaConiuge' => $persona->famigliaSenzaConiuge(),
            'gruppoAttuale' => $persona->gruppofamiliareAttuale(),
            'aziende' => $aziende,
            'incarichi' => $incarichi,
            'posizioniComunita' => Posizione::query()->whereIn('abbreviato', self::POSIZIONI_COMUNITA)->get(),
            'gruppiFamiliari' => GruppoFamiliare::query()->orderBy('nome')->get(),
            'aziendeAssegnabili' => Azienda::query()->where('tipo', 'azienda')->whereNotIn('id', $aziende->pluck('id'))->get(),
            'incarichiAssegnabili' => Incarico::query()->whereNotIn('id', $incarichi->pluck('id'))->orderBy('nome')->get(),
            'famiglia' => $famiglia,
            'nelNucleo' => $nelNucleo,
            'posizioneFamiglia' => $posizioneFamiglia,
            'unioni' => $unioni,
            'unione' => $unione,
            'posizioneUnione' => $posizioneUnione,
            'coniuge' => $coniuge,
            'figliNelNucleo' => $figliNelNucleo,
            'figliFuoriNucleo' => $figliFuoriNucleo,
            'origini' => $origini,
            'famigliaOrigine' => $famigliaOrigine,
            'genitori' => $genitori,
            'famigliaEnrico' => $famigliaEnrico,
            'periodi' => $this->periodiInNomadelfia($persona, $periodiPopolazione),
            'timeline' => $buildTimeline->execute($persona),
            'dateCollegate' => $dateCollegate,
            'periodoRiapribile' => $uscitaEliminabile ? $ultimoPeriodo : null,
            'riaperte' => $uscitaEliminabile ? $eliminaDataUscita->riaperte($persona, $ultimoPeriodo) : null,
        ]);
    }

    /**
     * The periods spent in Nomadelfia, positioned as percentages of the life of the person (from birth to today or death).
     *
     * @param  Collection<int, PopolazioneNomadelfia>  $periodiPopolazione
     * @return Collection<int, array{entrata: Carbon, uscita: ?Carbon, left: float, width: float}>
     */
    private function periodiInNomadelfia(Persona $persona, Collection $periodiPopolazione): Collection
    {
        $nascita = Date::parse($persona->data_nascita);
        $fine = $persona->data_decesso ? Date::parse($persona->data_decesso) : Date::now();
        $vita = max(1, $nascita->diffInDays($fine));

        return $periodiPopolazione
            ->sortBy('data_entrata')
            ->values()
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
