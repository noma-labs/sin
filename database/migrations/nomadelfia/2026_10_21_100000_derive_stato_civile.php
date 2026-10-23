<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SqlMigrations\SqlMigration;

final class DeriveStatoCivile extends SqlMigration
{
    /**
     * The checks that a stato sposato or vedovo can be derived from the famiglia (PersonaInNomadelfia::statoCivile()) before deleting it.
     *
     * @var array<string, string>
     */
    private const array CONTROLLI = [
        'i coniugi hanno date diverse dello stato sposato: scegli quella giusta' => "
            SELECT DISTINCT persone.id, persone.nominativo
            FROM famiglie_persone
            JOIN persone ON persone.id = famiglie_persone.persona_id
            WHERE famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            AND famiglie_persone.famiglia_id IN (
                SELECT coniugi.famiglia_id
                FROM famiglie_persone coniugi
                JOIN persone_stati ON persone_stati.persona_id = coniugi.persona_id
                JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'SPO'
                WHERE coniugi.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
                GROUP BY coniugi.famiglia_id
                HAVING COUNT(DISTINCT persone_stati.data_inizio) > 1
            )",
        'è vedovo/a, ma nella sua famiglia il coniuge non risulta deceduto' => "
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
                WHERE famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            )",
    ];

    /**
     * The people with a stato sposato whose date cannot be copied into a famiglia, because they are not capofamiglia or moglie
     * of a single famiglia with the coniuge (e.g. the coniuge lives outside Nomadelfia or is not in the anagrafe).
     */
    private const string SPOSATI_SENZA_CONIUGE = "
        SELECT DISTINCT persone.id, persone.nominativo, persone_stati.data_inizio
        FROM persone_stati
        JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'SPO'
        JOIN persone ON persone.id = persone_stati.persona_id
        WHERE (
            SELECT COUNT(*) FROM famiglie_persone
            WHERE famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        ) <> 1
        OR NOT EXISTS (
            SELECT 1 FROM famiglie_persone
            JOIN famiglie_persone coniuge ON coniuge.famiglia_id = famiglie_persone.famiglia_id
                AND coniuge.persona_id <> famiglie_persone.persona_id
                AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            WHERE famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        )
        ORDER BY persone.id";

    public $connection = 'db_nomadelfia';

    /**
     * Refuses to delete the stati sposato and vedovo when any of them contradicts the famiglia: those people must be fixed by hand before migrating.
     * A stato sposato without the coniuge in the famiglia is deleted anyway, and its date is logged because it is lost.
     *
     * @throws RuntimeException listing the people to fix; nothing is changed
     */
    public function up(): void
    {
        $problemi = $this->statiNonDerivabili();
        if ($problemi !== []) {
            throw new RuntimeException("Migrazione interrotta, nessun dato è stato modificato. Correggi a mano queste persone e rilancia:\n".implode("\n", $problemi));
        }

        $sposatiSenzaConiuge = $this->sposatiSenzaConiuge();

        parent::up();

        if ($sposatiSenzaConiuge !== []) {
            Log::warning("Stato sposato eliminato senza copiarne la data nella famiglia, perché il coniuge non risulta nella famiglia:\n".implode("\n", $sposatiSenzaConiuge));
        }
    }

    /**
     * @return list<string> one line per person whose stato sposato or vedovo contradicts the famiglia
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

    /**
     * @return list<string> one line per stato sposato whose date is lost, with the person and the date
     */
    public function sposatiSenzaConiuge(): array
    {
        return array_map(
            fn (object $persona): string => "- {$persona->nominativo} (id {$persona->id}): sposato/a dal {$persona->data_inizio}",
            DB::connection($this->connection)->select(self::SPOSATI_SENZA_CONIUGE),
        );
    }
}
