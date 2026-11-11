<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\DataCollegata;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Corrects the data di entrata of a period in Nomadelfia. The posizione and the gruppo familiare that started with the entrata
 * (the ones with the same data di inizio) get the new date too, to keep the percorso of the person consistent.
 */
final class ModificaDataEntrataAction
{
    public function execute(Persona $persona, string $dataEntrata, Carbon $nuovaDataEntrata): void
    {
        $nuovaData = $nuovaDataEntrata->toDateString();

        DB::connection('db_nomadelfia')->transaction(function () use ($persona, $dataEntrata, $nuovaData): void {
            DB::connection('db_nomadelfia')->table('popolazione')
                ->where('persona_id', $persona->id)
                ->where('data_entrata', $dataEntrata)
                ->update(['data_entrata' => $nuovaData]);

            $this->posizioni($persona, $dataEntrata)->update(['data_inizio' => $nuovaData]);
            $this->gruppi($persona, $dataEntrata)->update(['data_entrata_gruppo' => $nuovaData]);
        });
    }

    /**
     * What started with the entrata and moves with it to the new data di entrata.
     *
     * @return Collection<int, DataCollegata>
     */
    public function collegate(Persona $persona, string $dataEntrata): Collection
    {
        $posizioni = $this->posizioni($persona, $dataEntrata)
            ->join('posizioni', 'posizioni.id', '=', 'persone_posizioni.posizione_id')
            ->get(['posizioni.nome', 'persone_posizioni.data_inizio as inizio', 'persone_posizioni.data_fine as fine'])
            ->map(fn (stdClass $riga): DataCollegata => new DataCollegata('Posizione', $riga->nome, $riga->inizio, $riga->fine));

        $gruppi = $this->gruppi($persona, $dataEntrata)
            ->join('gruppi_familiari', 'gruppi_familiari.id', '=', 'gruppi_persone.gruppo_famigliare_id')
            ->get(['gruppi_familiari.nome', 'gruppi_persone.data_entrata_gruppo as inizio', 'gruppi_persone.data_uscita_gruppo as fine'])
            ->map(fn (stdClass $riga): DataCollegata => new DataCollegata('Gruppo familiare', $riga->nome, $riga->inizio, $riga->fine));

        return $posizioni->merge($gruppi)->values();
    }

    private function posizioni(Persona $persona, string $dataEntrata): Builder
    {
        return DB::connection('db_nomadelfia')->table('persone_posizioni')
            ->where('persone_posizioni.persona_id', $persona->id)
            ->where('persone_posizioni.data_inizio', $dataEntrata);
    }

    private function gruppi(Persona $persona, string $dataEntrata): Builder
    {
        return DB::connection('db_nomadelfia')->table('gruppi_persone')
            ->where('gruppi_persone.persona_id', $persona->id)
            ->where('gruppi_persone.data_entrata_gruppo', $dataEntrata);
    }
}
