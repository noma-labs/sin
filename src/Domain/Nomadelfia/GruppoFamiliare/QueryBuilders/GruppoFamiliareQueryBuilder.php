<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\GruppoFamiliare\QueryBuilders;

use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Illuminate\Database\Eloquent\Builder;

final class GruppoFamiliareQueryBuilder extends Builder
{
    public function single(GruppoFamiliare $gruppo): self
    {
        return $this->select('persone.*')
            ->from('persone')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->join('gruppi_persone', 'gruppi_persone.persona_id', '=', 'persone.id')
            ->whereNotIn('persone.id', function ($query): void {
                $query->select('famiglie_persone.persona_id')
                    ->from('famiglie_persone')
                    ->where('famiglie_persone.stato', '=', '1');
            })
            ->where('gruppi_persone.stato', '=', '1')
            ->where('gruppi_persone.gruppo_famigliare_id', '=', $gruppo->id)
            ->whereNull('popolazione.data_uscita')
            ->orderBy('persone.nominativo');
    }

    public function families(GruppoFamiliare $gruppo): self
    {
        return $this->select('famiglie_persone.famiglia_id', 'famiglie.nome_famiglia', 'persone.id as persona_id', 'persone.nominativo', 'famiglie_persone.posizione_famiglia', 'persone.data_nascita')
            ->from('gruppi_persone')
            ->leftJoin('famiglie_persone', 'famiglie_persone.persona_id', '=', 'gruppi_persone.persona_id')
            ->leftJoin('famiglie', 'famiglie_persone.famiglia_id', '=', 'famiglie.id')
            ->join('persone', 'persone.id', '=', 'gruppi_persone.persona_id')
            ->where('gruppi_persone.gruppo_famigliare_id', '=', $gruppo->id)
            ->where('gruppi_persone.stato', '=', '1')
            ->where('famiglie_persone.stato', '=', '1') // OR famiglie_persone.stato IS NULL)
            ->whereIn('famiglie_persone.posizione_famiglia', ['CAPO FAMIGLIA',
                'MOGLIE',
                'FIGLIO NATO',
                'FIGLIO ACCOLTO'])
            ->orderBy('famiglie.nome_famiglia')
            ->orderBy('persone.data_nascita', 'ASC');

        //        DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita
        //      FROM gruppi_persone
        //      LEFT JOIN famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id
        //      LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id
        //      INNER JOIN persone ON gruppi_persone.persona_id = persone.id
        //      WHERE gruppi_persone.gruppo_famigliare_id = :gruppo
        //          AND gruppi_persone.stato = '1'
        //          AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL)
        //          AND (famiglie_persone.posizione_famiglia != 'SINGLE' OR famiglie_persone.stato IS NULL)
        //      ORDER BY  famiglie.nome_famiglia, persone.data_nascita ASC"), ['gruppo' => $this->id]);

    }
}
