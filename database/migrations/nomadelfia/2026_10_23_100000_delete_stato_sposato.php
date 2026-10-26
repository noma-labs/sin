<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use SqlMigrations\SqlMigration;

final class DeleteStatoSposato extends SqlMigration
{
    /**
     * The stati sposato that get deleted, because the coniuge is in the famiglia, but whose date is not the data_creazione
     * of a famiglia where the person is capofamiglia or moglie: deleting them would lose the marriage date.
     */
    private const string SPOSATI_SENZA_DATA_IN_FAMIGLIA = "
        SELECT persone.id, persone.nominativo, persone_stati.data_inizio
        FROM persone_stati
        JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'SPO'
        JOIN persone ON persone.id = persone_stati.persona_id
        WHERE EXISTS (
            SELECT 1 FROM famiglie_persone
            JOIN famiglie_persone coniuge ON coniuge.famiglia_id = famiglie_persone.famiglia_id
                AND coniuge.persona_id <> famiglie_persone.persona_id
                AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            WHERE famiglie_persone.persona_id = persone.id
            AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        )
        AND NOT EXISTS (
            SELECT 1 FROM famiglie_persone
            JOIN famiglie ON famiglie.id = famiglie_persone.famiglia_id
            WHERE famiglie_persone.persona_id = persone.id
            AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            AND famiglie.data_creazione = persone_stati.data_inizio
        )
        ORDER BY persone.id";

    public $connection = 'db_nomadelfia';

    /**
     * Deletes the stato sposato of the people with the coniuge in the famiglia, only when every date of those stati sposato
     * is already stored as the data_creazione of the famiglia.
     * A sposato/a without coniuge in the famiglia (the coniuge lives outside Nomadelfia) keeps the stato SPO: see PersonaInNomadelfia::statoCivile().
     *
     * @throws RuntimeException listing the people to fix; nothing is changed
     */
    public function up(): void
    {
        $problemi = $this->sposatiSenzaDataInFamiglia();
        if ($problemi !== []) {
            throw new RuntimeException("Migrazione interrotta, nessun dato è stato modificato. La data dello stato sposato non è la data di creazione di una loro famiglia: correggi a mano queste persone e rilancia:\n".implode("\n", $problemi));
        }

        parent::up();
    }

    /**
     * @return list<string> one line per stato sposato whose date is not in a famiglia, with the person and the date
     */
    public function sposatiSenzaDataInFamiglia(): array
    {
        return array_map(
            fn (object $persona): string => "- {$persona->nominativo} (id {$persona->id}): sposato/a dal {$persona->data_inizio}",
            DB::connection($this->connection)->select(self::SPOSATI_SENZA_DATA_IN_FAMIGLIA),
        );
    }
}
