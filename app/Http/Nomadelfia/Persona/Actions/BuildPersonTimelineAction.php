<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Actions;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Persona\DataTransferObjects\TimelineEvent;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

/**
 * Builds the sequence of the most important events of a person inside Nomadelfia:
 * birth, entrate/uscite from the community, changes of posizione, stati (e.g. ordination), marriage and vedovanza, birth and accoglienza of the children, death.
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

        $evento = fn (Carbon $data, string $categoria, string $etichetta, string $titolo, ?string $dettaglio = null, ?Persona $altraPersona = null, bool $importante = false): TimelineEvent => new TimelineEvent(
            $data, (int) $nascita->diffInYears($data), $categoria, $etichetta, $titolo, $dettaglio, $altraPersona, $importante
        );

        $eventi = collect([$evento($nascita, TimelineEvent::CATEGORIA_ANAGRAFE, 'Nascita', $persona->provincia_nascita ?: 'Luogo non indicato')]);

        $posizioni = $persona->posizioni()->orderByPivot('data_inizio')->toBase()->get(['posizioni.nome', 'posizioni.abbreviato', 'persone_posizioni.data_inizio']);
        $dateEntrata = collect();
        $ultimaUscita = null;

        foreach (PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->orderBy('data_entrata')->get() as $periodo) {
            $entrata = Date::parse($periodo->data_entrata);
            $dateEntrata->push($entrata);
            $posizione = $posizioni->first(fn ($p): bool => Date::parse($p->data_inizio)->isSameDay($entrata));

            $eventi->push($evento(
                $entrata,
                TimelineEvent::CATEGORIA_COMUNITA,
                'Comunità',
                ($ultimaUscita ? 'Rientra' : 'Entra').' in Nomadelfia'.($posizione ? ' come '.$posizione->nome : ''),
                $ultimaUscita ? 'Dopo '.$ultimaUscita->diffForHumans($entrata, CarbonInterface::DIFF_ABSOLUTE, false, 2).' fuori dalla comunità' : null,
                importante: true,
            ));

            if ($periodo->data_uscita) {
                $ultimaUscita = Date::parse($periodo->data_uscita);
                if (! $decesso?->isSameDay($ultimaUscita)) {
                    $eventi->push($evento($ultimaUscita, TimelineEvent::CATEGORIA_COMUNITA, 'Comunità', 'Esce da Nomadelfia', importante: true));
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

        $famiglieSenzaConiuge = collect();
        foreach (['CAPO FAMIGLIA' => 'MOGLIE', 'MOGLIE' => 'CAPO FAMIGLIA'] as $posizioneFamiglia => $posizioneConiuge) {
            foreach ($persona->famiglie()->wherePivot('posizione_famiglia', $posizioneFamiglia)->get() as $famiglia) {
                if ($famiglia->data_creazione) {
                    $creazione = Date::parse($famiglia->data_creazione);
                    $coniuge = $famiglia->componenti()
                        ->wherePivot('posizione_famiglia', $posizioneConiuge)
                        ->first();
                    if ($coniuge) {
                        $eventi->push($evento(
                            $creazione,
                            TimelineEvent::CATEGORIA_FAMIGLIA,
                            'Famiglia',
                            'Sposa '.$coniuge->nominativo,
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
                                'Muore '.$coniuge->nominativo,
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
                            ($figlio->isMaschio() ? 'Nasce il figlio ' : 'Nasce la figlia ').$figlio->nominativo,
                            altraPersona: $figlio,
                        ));

                        continue;
                    }

                    $dataAccoglienza = PopolazioneNomadelfia::query()->where('persona_id', $figlio->id)->min('data_entrata');
                    if (is_string($dataAccoglienza)) {
                        $eventi->push($evento(
                            Date::parse($dataAccoglienza),
                            TimelineEvent::CATEGORIA_FAMIGLIA,
                            'Famiglia',
                            ($figlio->isMaschio() ? 'Accoglie il figlio ' : 'Accoglie la figlia ').$figlio->nominativo,
                            ($figlio->isMaschio() ? 'Nato il ' : 'Nata il ').Date::parse($figlio->data_nascita)->format('d/m/Y'),
                            $figlio,
                        ));
                    }
                }
            }
        }

        $dateStati = collect();
        foreach ($persona->stati()->orderByPivot('data_inizio')->toBase()->get(['stati.stato', 'stati.nome', 'persone_stati.data_inizio']) as $stato) {
            $inizio = Date::parse($stato->data_inizio);
            $dateStati->push($inizio);
            $eventi->push($evento(
                $inizio,
                TimelineEvent::CATEGORIA_FAMIGLIA,
                'Stato',
                match ($stato->stato) {
                    'SAC' => 'Ordinato sacerdote',
                    'MAV' => 'Diventa mamma di vocazione',
                    'MAM' => 'Diventa nomadelfa mamma',
                    'SEP' => $persona->isMaschio() ? 'Si separa dalla moglie' : 'Si separa dal marito',
                    default => Str::ucfirst(mb_strtolower($stato->nome)),
                },
                importante: in_array($stato->stato, ['SAC', 'MAV'], true),
            ));
        }

        foreach ($famiglieSenzaConiuge as ['data' => $creazione, 'famiglia' => $famiglia]) {
            if ($dateStati->doesntContain(fn (Carbon $data): bool => $data->isSameDay($creazione))) {
                $eventi->push($evento($creazione, TimelineEvent::CATEGORIA_FAMIGLIA, 'Famiglia', 'Nasce la famiglia '.$famiglia->nome_famiglia));
            }
        }

        if ($decesso) {
            $eventi->push($evento($decesso, TimelineEvent::CATEGORIA_ANAGRAFE, 'Decesso', 'Decesso', importante: true));
        }

        $primaNascita = fn (TimelineEvent $e): int => $e->etichetta === 'Nascita' ? 1 : 0;

        return $eventi
            ->sort(fn (TimelineEvent $a, TimelineEvent $b): int => [$b->data->getTimestamp(), $primaNascita($a)] <=> [$a->data->getTimestamp(), $primaNascita($b)])
            ->values();
    }
}
