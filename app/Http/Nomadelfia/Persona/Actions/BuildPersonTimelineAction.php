<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Actions;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\FamigliaPersona;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Persona\DataTransferObjects\TimelineEvent;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\View\Components\PersonaNome;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

/**
 * Builds the sequence of the most important events of a person inside Nomadelfia:
 * birth, entrate/uscite from the community, changes of posizione, stati (e.g. ordination), the famiglie the person
 * lived in (percorso familiare), marriage and vedovanza, birth and accoglienza of the children, death.
 */
final class BuildPersonTimelineAction
{
    /**
     * @return Collection<int, TimelineEvent> the events ordered from the most recent
     */
    public function execute(Persona $persona): Collection
    {
        $nascita = Date::parse($persona->data_nascita);
        $decesso = $persona->data_decesso ? Date::parse($persona->data_decesso) : null;

        $evento = fn (Carbon $data, string $categoria, string $etichetta, string $titolo, ?string $dettaglio = null, ?Persona $altraPersona = null, bool $importante = false, ?Famiglia $famiglia = null, bool $dataRegistrata = true, ?PopolazioneNomadelfia $periodo = null, ?string $confinePeriodo = null): TimelineEvent => new TimelineEvent(
            $data, (int) $nascita->diffInYears($data), $categoria, $etichetta, $titolo, $dettaglio, $altraPersona, $importante, $famiglia, $dataRegistrata, $periodo, $confinePeriodo
        );

        /** @var Collection<int, Famiglia&object{pivot: FamigliaPersona}> $percorsoFamiliare */
        $percorsoFamiliare = $persona->percorsoFamiliare()->get();
        $famigliaNascita = $percorsoFamiliare->first(fn ($famiglia): bool => $famiglia->pivot->posizione_famiglia === 'FIGLIO NATO');

        $eventi = collect([$evento(
            $nascita,
            TimelineEvent::CATEGORIA_ANAGRAFE,
            'Nascita',
            $persona->provincia_nascita ?: 'Luogo non indicato',
            $famigliaNascita ? 'Nella famiglia '.$famigliaNascita->nome_famiglia : null,
        )]);
        $eventi = $eventi->merge($this->eventiPercorsoFamiliare($persona, $percorsoFamiliare, $evento));

        $posizioni = $persona->posizioni()->orderByPivot('data_inizio')->toBase()->get(['posizioni.nome', 'posizioni.abbreviato', 'persone_posizioni.data_inizio']);
        $gruppi = $persona->gruppifamiliari()
            ->wherePivotNotNull('data_entrata_gruppo')
            ->orderByPivot('data_entrata_gruppo')
            ->toBase()
            ->get(['gruppi_familiari.nome', 'gruppi_persone.data_entrata_gruppo']);
        $dateEntrata = collect();
        $ultimaUscita = null;
        $periodoChiusoDalDecesso = null;

        foreach (PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->get()->sortBy('data_entrata') as $periodo) {
            $entrata = Date::parse($periodo->data_entrata);
            $dateEntrata->push($entrata);
            $posizione = $posizioni->first(fn ($p): bool => Date::parse($p->data_inizio)->isSameDay($entrata));
            $gruppo = $gruppi->first(fn ($g): bool => Date::parse($g->data_entrata_gruppo)->isSameDay($entrata));
            $dettaglio = collect([
                $ultimaUscita ? 'Dopo '.$ultimaUscita->diffForHumans($entrata, CarbonInterface::DIFF_ABSOLUTE, false, 2).' fuori dalla comunità' : null,
                $gruppo ? 'Nel gruppo familiare '.$gruppo->nome : null,
            ])->filter()->implode(' · ');

            $eventi->push($evento(
                $entrata,
                TimelineEvent::CATEGORIA_COMUNITA,
                'Comunità',
                ($ultimaUscita ? 'Rientra' : 'Entra').' in Nomadelfia'.($posizione ? ' come '.$posizione->nome : ''),
                $dettaglio !== '' ? $dettaglio : null,
                importante: true,
                periodo: $periodo,
                confinePeriodo: TimelineEvent::PERIODO_ENTRATA,
            ));

            if ($periodo->data_uscita) {
                $ultimaUscita = Date::parse($periodo->data_uscita);
                if ($decesso?->isSameDay($ultimaUscita)) {
                    $periodoChiusoDalDecesso = $periodo;
                } else {
                    $eventi->push($evento($ultimaUscita, TimelineEvent::CATEGORIA_COMUNITA, 'Comunità', 'Esce da Nomadelfia', importante: true, periodo: $periodo, confinePeriodo: TimelineEvent::PERIODO_USCITA));
                }
            }
        }

        $posizionePrecedente = null;
        foreach ($posizioni as $posizione) {
            $inizio = Date::parse($posizione->data_inizio);
            if ($dateEntrata->doesntContain(fn (Carbon $data): bool => $data->isSameDay($inizio))) {
                $eventi->push($evento(
                    $inizio,
                    TimelineEvent::CATEGORIA_COMUNITA,
                    'Posizione',
                    'Diventa '.$posizione->nome,
                    $posizionePrecedente ? 'Prima: '.$posizionePrecedente : null,
                    importante: $posizione->abbreviato === 'EFFE',
                ));
            }
            $posizionePrecedente = $posizione->nome;
        }

        // the entrata in a gruppo familiare on the day of the entrata in the comunità is told by the entrata itself
        $gruppoPrecedente = null;
        foreach ($gruppi as $gruppo) {
            $entrataGruppo = Date::parse($gruppo->data_entrata_gruppo);
            if ($dateEntrata->doesntContain(fn (Carbon $data): bool => $data->isSameDay($entrataGruppo))) {
                $eventi->push($evento(
                    $entrataGruppo,
                    TimelineEvent::CATEGORIA_COMUNITA,
                    'Gruppo familiare',
                    'Entra nel gruppo familiare '.$gruppo->nome,
                    $gruppoPrecedente ? 'Prima: '.$gruppoPrecedente : null,
                ));
            }
            $gruppoPrecedente = $gruppo->nome;
        }

        $famiglieSenzaConiuge = collect();
        foreach ($persona->famiglie()->wherePivotIn('posizione_famiglia', ['CAPO FAMIGLIA', 'MOGLIE'])->get() as $famiglia) {
            if ($famiglia->data_creazione) {
                $creazione = Date::parse($famiglia->data_creazione);
                // the other capofamiglia or moglie: also a deceased husband next to the vedova who heads the famiglia
                $coniuge = $famiglia->componenti()
                    ->wherePivotIn('posizione_famiglia', ['CAPO FAMIGLIA', 'MOGLIE'])
                    ->where('persone.id', '!=', $persona->id)
                    ->first();
                if ($coniuge) {
                    $eventi->push($evento(
                        $creazione,
                        TimelineEvent::CATEGORIA_FAMIGLIA,
                        'Famiglia',
                        'Sposa '.PersonaNome::testo($coniuge),
                        'Nasce la famiglia '.$famiglia->nome_famiglia,
                        $coniuge,
                        importante: true,
                    ));

                    $decessoConiuge = $coniuge->data_decesso ? Date::parse($coniuge->data_decesso) : null;
                    if ($decessoConiuge && (! $decesso || $decessoConiuge->lessThan($decesso))) {
                        $eventi->push($evento(
                            $decessoConiuge,
                            TimelineEvent::CATEGORIA_FAMIGLIA,
                            'Famiglia',
                            $persona->isMaschio() ? 'Rimane vedovo' : 'Rimane vedova',
                            'Muore '.PersonaNome::testo($coniuge),
                            $coniuge,
                        ));
                    }
                } else {
                    // a capofamiglia without coniuge, like a mamma di vocazione with her figli accolti
                    $famiglieSenzaConiuge->push(['data' => $creazione, 'famiglia' => $famiglia]);
                }
            }

            foreach ($famiglia->figli()->get() as $figlio) {
                if ($figlio->pivot->posizione_famiglia === 'FIGLIO NATO') {
                    $eventi->push($evento(
                        Date::parse($figlio->data_nascita),
                        TimelineEvent::CATEGORIA_FAMIGLIA,
                        'Famiglia',
                        ($figlio->isMaschio() ? 'Nasce il figlio ' : 'Nasce la figlia ').PersonaNome::testo($figlio),
                        altraPersona: $figlio,
                    ));

                    continue;
                }

                // the entrata in the famiglia of a figlio moved from another famiglia, else his entrata in the comunità
                $dataAccoglienza = $figlio->pivot->data_entrata
                    ?? PopolazioneNomadelfia::query()->where('persona_id', $figlio->id)->min('data_entrata');
                if (is_string($dataAccoglienza)) {
                    $eventi->push($evento(
                        Date::parse($dataAccoglienza),
                        TimelineEvent::CATEGORIA_FAMIGLIA,
                        'Famiglia',
                        ($figlio->pivot->posizione_famiglia === 'AGGREGATO' ? 'Aggrega ' : ($figlio->isMaschio() ? 'Accoglie il figlio ' : 'Accoglie la figlia ')).PersonaNome::testo($figlio),
                        ($figlio->isMaschio() ? 'Nato il ' : 'Nata il ').Date::parse($figlio->data_nascita)->format('d/m/Y'),
                        $figlio,
                    ));
                }
            }
        }

        foreach ($persona->stati()->orderByPivot('data_inizio')->toBase()->get(['stati.stato', 'stati.nome', 'persone_stati.data_inizio']) as $stato) {
            $eventi->push($evento(
                Date::parse($stato->data_inizio),
                TimelineEvent::CATEGORIA_FAMIGLIA,
                'Stato',
                match ($stato->stato) {
                    'SAC' => 'Ordinato sacerdote',
                    'SEP' => $persona->isMaschio() ? 'Si separa dalla moglie' : 'Si separa dal marito',
                    default => Str::ucfirst(mb_strtolower($stato->nome)),
                },
                importante: $stato->stato === 'SAC',
            ));
        }

        foreach ($famiglieSenzaConiuge as ['data' => $creazione, 'famiglia' => $famiglia]) {
            if ($famiglia->tipo?->isMamma()) {
                $eventi->push($evento(
                    $creazione,
                    TimelineEvent::CATEGORIA_FAMIGLIA,
                    'Famiglia',
                    'Diventa '.mb_strtolower($famiglia->tipo->nome()),
                    'Nasce la famiglia '.$famiglia->nome_famiglia,
                    importante: $famiglia->tipo === TipoFamiglia::MammaDiVocazione,
                ));

                continue;
            }
            $eventi->push($evento($creazione, TimelineEvent::CATEGORIA_FAMIGLIA, 'Famiglia', 'Nasce la famiglia '.$famiglia->nome_famiglia));
        }

        if ($decesso) {
            $eventi->push($evento(
                $decesso,
                TimelineEvent::CATEGORIA_ANAGRAFE,
                'Decesso',
                'Decesso',
                importante: true,
                periodo: $periodoChiusoDalDecesso,
                confinePeriodo: $periodoChiusoDalDecesso ? TimelineEvent::PERIODO_USCITA : null,
            ));
        }

        $primaNascita = fn (TimelineEvent $e): int => $e->etichetta === 'Nascita' ? 1 : 0;

        return $eventi
            ->sort(fn (TimelineEvent $a, TimelineEvent $b): int => [$b->data->getTimestamp(), $primaNascita($a)] <=> [$a->data->getTimestamp(), $primaNascita($b)])
            ->values();
    }

    /**
     * The entrata of the persona in the famiglie of the percorso familiare not already told by another event: the
     * ones as figlio accolto or aggregato, at the data_entrata recorded in the famiglia or, for the first famiglia of
     * the persona, at the first entrata in the comunità, and the ones as capofamiglia or moglie without data_creazione.
     * The birth in the famiglia of a figlio nato is in the event of the birth, the famiglia with a data_creazione of a
     * capofamiglia or moglie in the event of the marriage or of the nascita of the famiglia.
     * A famiglia without a known date keeps its place in the percorso familiare: right after the previous famiglia.
     *
     * @param  Collection<int, Famiglia&object{pivot: FamigliaPersona}>  $percorsoFamiliare  ordered from the first to the last famiglia
     * @param  Closure(Carbon, string, string, string, ?string=, ?Persona=, bool=, ?Famiglia=, bool=): TimelineEvent  $evento
     * @return Collection<int, TimelineEvent>
     */
    private function eventiPercorsoFamiliare(Persona $persona, Collection $percorsoFamiliare, Closure $evento): Collection
    {
        $maschio = $persona->isMaschio();
        $primaFamiglia = $percorsoFamiliare->first();
        $dataPrecedente = Date::parse($persona->data_nascita);
        $eventi = collect();

        foreach ($percorsoFamiliare as $famiglia) {
            $posizione = $famiglia->pivot->posizione_famiglia;
            $isConiuge = in_array($posizione, ['CAPO FAMIGLIA', 'MOGLIE'], true);
            $data = match (true) {
                $posizione === 'FIGLIO NATO' => Date::parse($persona->data_nascita),
                $isConiuge => $famiglia->data_creazione ? Date::parse($famiglia->data_creazione) : null,
                $famiglia->pivot->data_entrata !== null => Date::parse($famiglia->pivot->data_entrata),
                $famiglia->is($primaFamiglia) => $this->primaEntrata($persona),
                default => null,
            };

            if ($data instanceof Carbon && ($posizione === 'FIGLIO NATO' || $isConiuge)) {
                $dataPrecedente = $data;

                continue;
            }

            $dataRegistrata = $data instanceof Carbon;
            $dataPrecedente = $data ?? $dataPrecedente->copy()->addSecond();
            $eventi->push($evento(
                $dataPrecedente,
                TimelineEvent::CATEGORIA_FAMIGLIA,
                'Famiglia',
                match ($posizione) {
                    'FIGLIO ACCOLTO' => ($maschio ? 'Accolto' : 'Accolta').' nella famiglia ',
                    'AGGREGATO' => ($maschio ? 'Aggregato' : 'Aggregata').' alla famiglia ',
                    'CAPO FAMIGLIA' => 'Capofamiglia della famiglia ',
                    'MOGLIE' => 'Moglie nella famiglia ',
                    default => 'Nella famiglia ',
                }.$famiglia->nome_famiglia,
                null,
                null,
                false,
                $famiglia,
                $dataRegistrata,
            ));
        }

        return $eventi;
    }

    private function primaEntrata(Persona $persona): ?Carbon
    {
        $entrata = PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->min('data_entrata');

        return is_string($entrata) ? Date::parse($entrata) : null;
    }
}
