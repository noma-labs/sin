<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use SqlMigrations\SqlMigration;

final class DeleteStatiDerivati extends SqlMigration
{
    /**
     * The checks that the active stati celibe, nubile, vedovo, mamma and celibe di elezione are already derived from the famiglia.
     *
     * @var array<string, string>
     */
    private const array CONTROLLI = [
        'è vedovo/a, ma il coniuge nella sua famiglia non è deceduto' => "
            SELECT DISTINCT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'VED'
            JOIN persone ON persone.id = persone_stati.persona_id
            WHERE persone_stati.stato = '1'
            AND NOT EXISTS (
                SELECT 1 FROM famiglie_persone
                JOIN famiglie_persone coniuge ON coniuge.famiglia_id = famiglie_persone.famiglia_id
                    AND coniuge.persona_id <> famiglie_persone.persona_id
                    AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
                JOIN persone defunto ON defunto.id = coniuge.persona_id AND defunto.data_decesso IS NOT NULL
                WHERE famiglie_persone.persona_id = persone.id
                AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            )",
        'ha uno stato mamma, ma non è capofamiglia di una famiglia di quel tipo' => "
            SELECT DISTINCT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAM', 'MAN')
            JOIN persone ON persone.id = persone_stati.persona_id
            WHERE persone_stati.stato = '1'
            AND NOT EXISTS (
                SELECT 1 FROM famiglie_persone
                JOIN famiglie ON famiglie.id = famiglie_persone.famiglia_id AND famiglie.tipo = stati.stato
                WHERE famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
            )",
        'è celibe di elezione, ma non è capofamiglia di una sola famiglia' => "
            SELECT DISTINCT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'CDE'
            JOIN persone ON persone.id = persone_stati.persona_id
            WHERE persone_stati.stato = '1'
            AND (
                SELECT COUNT(DISTINCT famiglie_persone.famiglia_id) FROM famiglie_persone
                WHERE famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
            ) <> 1",
        "è celibe di elezione, ma nella sua famiglia c'è un coniuge (capofamiglia o moglie) o un altro tipo" => "
            SELECT DISTINCT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'CDE'
            JOIN persone ON persone.id = persone_stati.persona_id
            JOIN famiglie_persone ON famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
            JOIN famiglie ON famiglie.id = famiglie_persone.famiglia_id
            WHERE persone_stati.stato = '1'
            AND (
                famiglie.tipo <> 'CDE'
                OR EXISTS (
                    SELECT 1 FROM famiglie_persone coniuge
                    WHERE coniuge.famiglia_id = famiglie.id
                    AND coniuge.persona_id <> persone.id
                    AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
                )
            )",
    ];

    public $connection = 'db_nomadelfia';

    /**
     * Moves the celibe di elezione into the tipo of the famiglia and deletes the stati derived from the famiglia,
     * keeping only sacerdote and separato.
     * Refuses when an active stato is not derived from the famiglia: those people must be fixed by hand before migrating.
     *
     * @throws RuntimeException listing the people to fix; nothing is changed
     */
    public function up(): void
    {
        $problemi = $this->statiNonDerivabili();
        if ($problemi !== []) {
            throw new RuntimeException("Migrazione interrotta, nessun dato è stato modificato. Correggi a mano queste persone e rilancia:\n".implode("\n", $problemi));
        }

        parent::up();
    }

    /**
     * @return list<string> one line per person whose active stato is not derived from the famiglia
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
