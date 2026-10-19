<?php

declare(strict_types=1);

namespace App\Nomadelfia\Concerns;

use App\Nomadelfia\Exceptions\FamigliaHasMultipleGroup;
use App\Nomadelfia\Exceptions\FamigliaHasNoGroup;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Relations and behaviour of a Famiglia inside the Nomadelfia community: gruppi familiari and uscita.
 */
trait FamigliaInNomadelfia
{
    public static function famiglieNumerose(int $min_componenti = 5)
    {
        $expression = DB::raw("WITH famiglie_numerose AS (
                             SELECT f.id, count(*) as componenti
                             FROM `famiglie`  f
                             INNER JOIN famiglie_persone fp ON fp.famiglia_id = f.id
                             INNER JOIN popolazione pop ON pop.persona_id = fp.persona_id
                             WHERE fp.stato = '1' and pop.data_uscita is NULL
                             GROUP BY f.id
                             HAVING componenti >= :minc
                             ORDER BY componenti DESC
                    ) select ff.*, famiglie_numerose.componenti
                    from famiglie_numerose
                    join famiglie ff on famiglie_numerose.id = ff.id
                    order by famiglie_numerose.componenti DESC;");

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['minc' => $min_componenti]
        );
    }

    public function uscita(Carbon $data_uscita): void
    {
        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $this->componentiAttuali()->get()->each(function ($componente) use ($data_uscita): void {
                $act = resolve(UscitaPersonaAction::class);
                $act->execute($componente, $data_uscita);
            });
            DB::connection('db_nomadelfia')->commit();
        } catch (Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }

    /**
     * Returns the current family group in which the HEAD OF FAMILY lives
     * It is assumed that the whole family lives in the same group as the HEAD OF FAMILY
     *
     **/
    public function gruppoFamiliareAttuale()
    {
        $expression = DB::raw("SELECT gruppi_familiari.*, gruppi_persone.data_entrata_gruppo
            FROM famiglie_persone
            INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
            INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
            WHERE famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
                and famiglie_persone.famiglia_id = :famiglia_id and gruppi_persone.stato = '1' and famiglie_persone.stato = '1'");
        $res = collect(DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['famiglia_id' => $this->id]
        ));
        if ($res->count() === 1) {
            return $res->first();
        }
        if ($res->count() === 0) {
            return null;
        }
        throw FamigliaHasMultipleGroup::named($this);
    }

    public function gruppoFamiliareAttualeOrFail()
    {
        $gruppo = $this->gruppoFamiliareAttuale();
        if ($gruppo === null) {
            throw FamigliaHasNoGroup::named($this->nome_famiglia);
        }

        return $gruppo;
    }

    /**
     * Returns the historical family groups in which the HEAD OF FAMILY of the family has lived
     **/
    public function gruppiFamiliariStorico()
    {
        $expression = DB::raw("SELECT gruppi_familiari.*, gruppi_persone.data_entrata_gruppo, gruppi_persone.data_uscita_gruppo
      FROM famiglie_persone
      INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
      INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
      WHERE (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA')
       and famiglie_persone.famiglia_id = :famiglia_id and gruppi_persone.stato = '0'");

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['famiglia_id' => $this->id]
        );
    }

    /**
     * Removes all family members from a family group
     *
     **/
    public function rimuoviDaGruppoFamiliare($idGruppo): void
    {
        $expression = DB::raw("UPDATE gruppi_persone
              SET
                  gruppi_persone.stato = '0'
              WHERE
                gruppi_persone.gruppo_famigliare_id = :gruppoattuale
                AND gruppi_persone.persona_id IN (
                      SELECT persone.id
                      FROM famiglie_persone
                      INNER JOIN persone ON persone.id = famiglie_persone.persona_id
                      #INNER join gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
                      WHERE famiglie_persone.famiglia_id = :famigliaId  AND famiglie_persone.stato = '1' #AND gruppi_persone.stato = '1'
                )
                AND gruppi_persone.stato = '1' ");
        DB::connection('db_nomadelfia')->update(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['gruppoattuale' => $idGruppo, 'famigliaId' => $this->id]
        );
    }

    /**
     * Assigns a new family group to the family.
     *
     **/
    public function assegnaFamigliaANuovoGruppoFamiliare(
        $gruppo_attuale_id,
        $dataUscitaGruppoFamiliareAttuale,
        $gruppo_nuovo_id,
        $data_entrata = null
    ) {
        $famiglia_id = $this->id;

        return DB::transaction(function () use (
            &$gruppo_attuale_id,
            $dataUscitaGruppoFamiliareAttuale,
            &$famiglia_id,
            &$gruppo_nuovo_id,
            &$data_entrata
        ): bool {

            // Disabilita tutti i componenti della famiglia dal vecchio gruppo (mette stato = 0)
            $expression = DB::raw("UPDATE gruppi_persone
              SET
                  gruppi_persone.stato = '0', data_uscita_gruppo = :data_uscita
              WHERE
                gruppi_persone.gruppo_famigliare_id = :gruppoattuale
                AND gruppi_persone.persona_id IN (
                      SELECT persone.id
                      FROM famiglie_persone
                      INNER JOIN persone ON persone.id = famiglie_persone.persona_id
                      #INNER join gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
                      WHERE famiglie_persone.famiglia_id = :famigliaId  AND famiglie_persone.stato = '1' #AND gruppi_persone.stato = '1'
                )

                AND gruppi_persone.stato = '1' ");

            DB::connection('db_nomadelfia')->update(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                [
                    'gruppoattuale' => $gruppo_attuale_id,
                    'famigliaId' => $famiglia_id,
                    'data_uscita' => $dataUscitaGruppoFamiliareAttuale,
                ]
            );

            // Aggiungi a tutti i componenti della famiglia nel nuovo gruppo
            $expr = DB::raw("INSERT INTO gruppi_persone (persona_id, gruppo_famigliare_id, stato, data_entrata_gruppo)
              SELECT persone.id, :gruppo_nuovo_id, '1', :data_entrata
              FROM famiglie_persone
              INNER JOIN persone ON persone.id = famiglie_persone.persona_id
              WHERE famiglie_persone.famiglia_id = :famigliaId   AND famiglie_persone.stato = '1' ");
            DB::connection('db_nomadelfia')->update(
                $expr->getValue(DB::connection()->getQueryGrammar()),
                [
                    'famigliaId' => $famiglia_id,
                    'gruppo_nuovo_id' => $gruppo_nuovo_id,
                    'data_entrata' => $data_entrata,
                ]// , 'data_uscita'=>$dataUscitaGruppoFamiliareAttuale)
            );

            return true;
        });
    }
}
