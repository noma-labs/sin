<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\DataCollegata;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Removes the data di uscita of the last period of a person, recorded by mistake or not known: the period is open again
 * and the person is in the popolazione attuale. The posizione and the gruppo familiare that the uscita concluded are reopened;
 * the aziende, the incarichi and the classe stay concluded.
 */
final class EliminaDataUscitaAction
{
    public function execute(Persona $persona, PopolazioneNomadelfia $periodo): void
    {
        DB::connection('db_nomadelfia')->transaction(function () use ($persona, $periodo): void {
            DB::connection('db_nomadelfia')->table('popolazione')
                ->where('persona_id', $persona->id)
                ->where('data_entrata', $periodo->data_entrata)
                ->update(['data_uscita' => null]);

            $dataUscita = $periodo->data_uscita;
            if ($dataUscita !== null) {
                $this->posizione($persona, $dataUscita)->update(['data_fine' => null, 'stato' => '1']);
                $this->gruppo($persona, $dataUscita)->update(['data_uscita_gruppo' => null, 'stato' => '1']);
            }
        });
    }

    /**
     * Why the data di uscita of the period cannot be removed, or null when it can: only the uscita of the last period
     * of a living person, so that the person has at most one period without uscita.
     */
    public function motivoRifiuto(Persona $persona, PopolazioneNomadelfia $periodo): ?string
    {
        if ($persona->isDeceduta()) {
            return 'La data di uscita di una persona deceduta non può essere eliminata.';
        }
        $isUltimoPeriodo = ! PopolazioneNomadelfia::query()
            ->where('persona_id', $persona->id)
            ->where('data_entrata', '>', $periodo->data_entrata)
            ->exists();

        return $isUltimoPeriodo ? null : "Si può eliminare solo la data di uscita dell'ultimo periodo in Nomadelfia.";
    }

    /**
     * The posizione and the gruppo familiare that the uscita concluded and are reopened.
     *
     * @return Collection<int, DataCollegata>
     */
    public function riaperte(Persona $persona, PopolazioneNomadelfia $periodo): Collection
    {
        $dataUscita = $periodo->data_uscita;
        if ($dataUscita === null) {
            return collect();
        }

        $posizione = $this->posizione($persona, $dataUscita)
            ->join('posizioni', 'posizioni.id', '=', 'persone_posizioni.posizione_id')
            ->first(['posizioni.nome', 'persone_posizioni.data_inizio as inizio', 'persone_posizioni.data_fine as fine']);
        $gruppo = $this->gruppo($persona, $dataUscita)
            ->join('gruppi_familiari', 'gruppi_familiari.id', '=', 'gruppi_persone.gruppo_famigliare_id')
            ->first(['gruppi_familiari.nome', 'gruppi_persone.data_entrata_gruppo as inizio', 'gruppi_persone.data_uscita_gruppo as fine']);

        $riaperte = [];
        if ($posizione instanceof stdClass) {
            $riaperte[] = new DataCollegata('Posizione', $posizione->nome, $posizione->inizio, $posizione->fine);
        }
        if ($gruppo instanceof stdClass) {
            $riaperte[] = new DataCollegata('Gruppo familiare', $gruppo->nome, $gruppo->inizio, $gruppo->fine);
        }

        return collect($riaperte);
    }

    /**
     * The last posizione concluded on the data di uscita: only one is reopened, so the person never has two posizioni attuali.
     */
    private function posizione(Persona $persona, string $dataUscita): Builder
    {
        return DB::connection('db_nomadelfia')->table('persone_posizioni')
            ->where('persone_posizioni.persona_id', $persona->id)
            ->where('persone_posizioni.data_fine', $dataUscita)
            ->orderByDesc('persone_posizioni.data_inizio')
            ->limit(1);
    }

    /**
     * The last gruppo familiare left on the data di uscita: only one is reopened, so the person never has two gruppi attuali.
     */
    private function gruppo(Persona $persona, string $dataUscita): Builder
    {
        return DB::connection('db_nomadelfia')->table('gruppi_persone')
            ->where('gruppi_persone.persona_id', $persona->id)
            ->where('gruppi_persone.data_uscita_gruppo', $dataUscita)
            ->orderByDesc('gruppi_persone.data_entrata_gruppo')
            ->limit(1);
    }
}
