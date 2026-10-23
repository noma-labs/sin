<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SqlMigrations\SqlMigration;

final class DeriveStatoCivile extends SqlMigration
{
    /**
     * The checks that a stato sposato can be derived from the famiglia (PersonaInNomadelfia::statoCivile()) before deleting it.
     * TODO: when the stato vedovo gets deleted too, check that the coniuge of a vedovo/a is deceased.
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
    ];

    /**
     * The people with a stato sposato whose date cannot be copied into a famiglia,
     * because they are not capofamiglia or moglie of exactly one famiglia.
     */
    private const string SPOSATI_SENZA_FAMIGLIA = "
        SELECT DISTINCT persone.id, persone.nominativo, persone_stati.data_inizio
        FROM persone_stati
        JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'SPO'
        JOIN persone ON persone.id = persone_stati.persona_id
        WHERE (
            SELECT COUNT(*) FROM famiglie_persone
            WHERE famiglie_persone.persona_id = persone.id AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        ) <> 1
        ORDER BY persone.id";

    public $connection = 'db_nomadelfia';

    /**
     * Refuses to delete the stati sposato when any of them contradicts the famiglia: those people must be fixed by hand before migrating.
     * A stato sposato of a capofamiglia alone (the coniuge is not in the famiglia) still gives its date to the famiglia.
     * A stato sposato that is not in exactly one famiglia is deleted anyway, and its date is logged because it is lost.
     *
     * @throws RuntimeException listing the people to fix; nothing is changed
     */
    public function up(): void
    {
        $problemi = $this->statiNonDerivabili();
        if ($problemi !== []) {
            throw new RuntimeException("Migrazione interrotta, nessun dato è stato modificato. Correggi a mano queste persone e rilancia:\n".implode("\n", $problemi));
        }

        $sposatiSenzaFamiglia = $this->sposatiSenzaFamiglia();

        parent::up();

        if ($sposatiSenzaFamiglia !== []) {
            Log::warning("Stato sposato eliminato senza copiarne la data, perché la persona non è capofamiglia o moglie di una sola famiglia:\n".implode("\n", $sposatiSenzaFamiglia));
        }
    }

    /**
     * @return list<string> one line per person whose stato sposato contradicts the famiglia
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
    public function sposatiSenzaFamiglia(): array
    {
        return array_map(
            fn (object $persona): string => "- {$persona->nominativo} (id {$persona->id}): sposato/a dal {$persona->data_inizio}",
            DB::connection($this->connection)->select(self::SPOSATI_SENZA_FAMIGLIA),
        );
    }
}
