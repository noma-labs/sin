<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SqlMigrations\SqlMigration;

final class DeriveMammeFromFamiglia extends SqlMigration
{
    /**
     * The checks that a stato mamma di vocazione or mamma nubile can become the tipo of the famiglia before deleting it.
     *
     * @var array<string, string>
     */
    private const array CONTROLLI = [
        'è mamma di vocazione o mamma nubile, ma nella sua famiglia c\'è il coniuge (capofamiglia o moglie)' => "
            SELECT DISTINCT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAN')
            JOIN persone ON persone.id = persone_stati.persona_id
            JOIN famiglie_persone ON famiglie_persone.persona_id = persone.id
                AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            JOIN famiglie_persone coniuge ON coniuge.famiglia_id = famiglie_persone.famiglia_id
                AND coniuge.persona_id <> persone.id
                AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            WHERE persone_stati.stato = '1'",
        'è sia mamma di vocazione sia mamma nubile: lascia attivo solo uno dei due stati' => "
            SELECT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAN')
            JOIN persone ON persone.id = persone_stati.persona_id
            WHERE persone_stati.stato = '1'
            GROUP BY persone.id, persone.nominativo
            HAVING COUNT(DISTINCT stati.stato) > 1",
    ];

    /**
     * The stati mamma di vocazione or mamma nubile that cannot become the tipo of a famiglia: closed ones,
     * or of people who are not capofamiglia of exactly one famiglia.
     */
    private const string MAMME_SENZA_FAMIGLIA = "
        SELECT persone.id, persone.nominativo, stati.nome, persone_stati.data_inizio, persone_stati.data_fine
        FROM persone_stati
        JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAN')
        JOIN persone ON persone.id = persone_stati.persona_id
        WHERE persone_stati.stato = '0'
        OR (
            SELECT COUNT(*) FROM famiglie_persone
            WHERE famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        ) <> 1
        OR NOT EXISTS (
            SELECT 1 FROM famiglie_persone
            WHERE famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
        )
        ORDER BY persone.id, persone_stati.data_inizio";

    /**
     * The famiglie of type CONIUGI with the capofamiglia and no coniuge: from now on the capofamiglia is shown as sposato/a.
     */
    private const string CONIUGI_SENZA_CONIUGE = "
        SELECT famiglie.id, famiglie.nome_famiglia, famiglie.data_creazione
        FROM famiglie
        WHERE famiglie.tipo = 'CONIUGI'
        AND EXISTS (
            SELECT 1 FROM famiglie_persone
            WHERE famiglie_persone.famiglia_id = famiglie.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
        )
        AND (
            SELECT COUNT(*) FROM famiglie_persone
            WHERE famiglie_persone.famiglia_id = famiglie.id AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        ) = 1
        ORDER BY famiglie.id";

    public $connection = 'db_nomadelfia';

    /**
     * Refuses to delete the stati mamma di vocazione and mamma nubile when any of them contradicts the famiglia: those people must be fixed by hand before migrating.
     * The stati that cannot become the tipo of a famiglia are deleted anyway and logged, together with the famiglie CONIUGI without coniuge to review.
     *
     * @throws RuntimeException listing the people to fix; nothing is changed
     */
    public function up(): void
    {
        $problemi = $this->statiNonDerivabili();
        if ($problemi !== []) {
            throw new RuntimeException("Migrazione interrotta, nessun dato è stato modificato. Correggi a mano queste persone e rilancia:\n".implode("\n", $problemi));
        }

        $mammeSenzaFamiglia = array_map(
            fn (object $mamma): string => "- {$mamma->nominativo} (id {$mamma->id}): {$mamma->nome} dal {$mamma->data_inizio}".($mamma->data_fine ? " al {$mamma->data_fine}" : ''),
            DB::connection($this->connection)->select(self::MAMME_SENZA_FAMIGLIA),
        );

        parent::up();

        if ($mammeSenzaFamiglia !== []) {
            Log::warning("Stato mamma di vocazione o mamma nubile eliminato senza riportarlo nella famiglia, perché chiuso o la persona non è capofamiglia di una sola famiglia:\n".implode("\n", $mammeSenzaFamiglia));
        }

        $coniugiSenzaConiuge = array_map(
            fn (object $famiglia): string => "- {$famiglia->nome_famiglia} (id {$famiglia->id}): sposato/a dal {$famiglia->data_creazione}",
            DB::connection($this->connection)->select(self::CONIUGI_SENZA_CONIUGE),
        );
        if ($coniugiSenzaConiuge !== []) {
            Log::warning("Famiglie con il solo capofamiglia di tipo CONIUGI, ora mostrato come sposato/a: controlla il tipo di famiglia:\n".implode("\n", $coniugiSenzaConiuge));
        }
    }

    /**
     * @return list<string> one line per person whose stato mamma di vocazione or mamma nubile contradicts the famiglia
     */
    public function statiNonDerivabili(): array
    {
        $problemi = [];
        foreach (self::CONTROLLI as $problema => $query) {
            foreach (DB::connection($this->connection)->select($query) as $persona) {
                $problemi[] = "- {$persona->nominativo} (id {$persona->id}): {$problema}";
            }
        }

        return $problemi;
    }
}
