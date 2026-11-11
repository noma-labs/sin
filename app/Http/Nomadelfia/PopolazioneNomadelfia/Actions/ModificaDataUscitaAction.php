<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\DataCollegata;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Corrects the data di uscita of a closed period in Nomadelfia. What the uscita concluded (the posizione, the gruppo familiare,
 * the aziende, the incarichi and the classe with the same data di fine) gets the new date too, to keep the percorso of the person consistent.
 */
final class ModificaDataUscitaAction
{
    public function execute(Persona $persona, string $dataUscita, Carbon $nuovaDataUscita): void
    {
        $nuovaData = $nuovaDataUscita->toDateString();

        DB::connection('db_nomadelfia')->transaction(function () use ($persona, $dataUscita, $nuovaData): void {
            DB::connection('db_nomadelfia')->table('popolazione')
                ->where('persona_id', $persona->id)
                ->where('data_uscita', $dataUscita)
                ->update(['data_uscita' => $nuovaData]);

            $this->posizioni($persona, $dataUscita)->update(['data_fine' => $nuovaData]);
            $this->gruppi($persona, $dataUscita)->update(['data_uscita_gruppo' => $nuovaData]);
            $this->aziende($persona, $dataUscita)->update(['data_fine_azienda' => $nuovaData]);
            $this->incarichi($persona, $dataUscita)->update(['data_fine' => $nuovaData]);
            $this->classi($persona, $dataUscita)->update(['data_fine' => $nuovaData]);
        });
    }

    /**
     * What the uscita concluded and moves with it to the new data di uscita.
     *
     * @return Collection<int, DataCollegata>
     */
    public function collegate(Persona $persona, string $dataUscita): Collection
    {
        $riga = fn (string $tipo): Closure => fn (stdClass $riga): DataCollegata => new DataCollegata($tipo, $riga->nome, $riga->inizio, $riga->fine);

        return collect()
            ->merge($this->posizioni($persona, $dataUscita)
                ->join('posizioni', 'posizioni.id', '=', 'persone_posizioni.posizione_id')
                ->get(['posizioni.nome', 'persone_posizioni.data_inizio as inizio', 'persone_posizioni.data_fine as fine'])
                ->map($riga('Posizione')))
            ->merge($this->gruppi($persona, $dataUscita)
                ->join('gruppi_familiari', 'gruppi_familiari.id', '=', 'gruppi_persone.gruppo_famigliare_id')
                ->get(['gruppi_familiari.nome', 'gruppi_persone.data_entrata_gruppo as inizio', 'gruppi_persone.data_uscita_gruppo as fine'])
                ->map($riga('Gruppo familiare')))
            ->merge($this->aziende($persona, $dataUscita)
                ->join('aziende', 'aziende.id', '=', 'aziende_persone.azienda_id')
                ->get(['aziende.nome_azienda as nome', 'aziende_persone.data_inizio_azienda as inizio', 'aziende_persone.data_fine_azienda as fine'])
                ->map($riga('Azienda')))
            ->merge($this->incarichi($persona, $dataUscita)
                ->join('incarichi', 'incarichi.id', '=', 'incarichi_persone.incarico_id')
                ->get(['incarichi.nome', 'incarichi_persone.data_inizio as inizio', 'incarichi_persone.data_fine as fine'])
                ->map($riga('Incarico')))
            ->merge($this->classi($persona, $dataUscita)
                ->join('db_scuola.classi', 'db_scuola.classi.id', '=', 'db_scuola.alunni_classi.classe_id')
                ->join('db_scuola.tipo', 'db_scuola.tipo.id', '=', 'db_scuola.classi.tipo_id')
                ->get(['db_scuola.tipo.nome', 'db_scuola.alunni_classi.data_inizio as inizio', 'db_scuola.alunni_classi.data_fine as fine'])
                ->map($riga('Classe')))
            ->values();
    }

    private function posizioni(Persona $persona, string $dataUscita): Builder
    {
        return DB::connection('db_nomadelfia')->table('persone_posizioni')
            ->where('persone_posizioni.persona_id', $persona->id)
            ->where('persone_posizioni.data_fine', $dataUscita);
    }

    private function gruppi(Persona $persona, string $dataUscita): Builder
    {
        return DB::connection('db_nomadelfia')->table('gruppi_persone')
            ->where('gruppi_persone.persona_id', $persona->id)
            ->where('gruppi_persone.data_uscita_gruppo', $dataUscita);
    }

    private function aziende(Persona $persona, string $dataUscita): Builder
    {
        return DB::connection('db_nomadelfia')->table('aziende_persone')
            ->where('aziende_persone.persona_id', $persona->id)
            ->where('aziende_persone.data_fine_azienda', $dataUscita);
    }

    private function incarichi(Persona $persona, string $dataUscita): Builder
    {
        return DB::connection('db_nomadelfia')->table('incarichi_persone')
            ->where('incarichi_persone.persona_id', $persona->id)
            ->where('incarichi_persone.data_fine', $dataUscita);
    }

    private function classi(Persona $persona, string $dataUscita): Builder
    {
        return DB::connection('db_nomadelfia')->table('db_scuola.alunni_classi')
            ->where('db_scuola.alunni_classi.persona_id', $persona->id)
            ->where('db_scuola.alunni_classi.data_fine', $dataUscita);
    }
}
