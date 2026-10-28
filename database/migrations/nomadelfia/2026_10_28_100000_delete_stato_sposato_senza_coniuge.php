<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use SqlMigrations\SqlMigration;

final class DeleteStatoSposatoSenzaConiuge extends SqlMigration
{
    /**
     * The stati sposato whose date is not the data_creazione of a famiglia of tipo coniugi
     * where the person is capofamiglia or moglie: deleting them would lose the matrimonio or its date.
     */
    private const string SPOSATI_SENZA_FAMIGLIA_CONIUGI = "
        SELECT persone.id, persone.nominativo, persone_stati.data_inizio
        FROM persone_stati
        JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'SPO'
        JOIN persone ON persone.id = persone_stati.persona_id
        WHERE NOT EXISTS (
            SELECT 1 FROM famiglie_persone
            JOIN famiglie ON famiglie.id = famiglie_persone.famiglia_id AND famiglie.tipo = 'CON'
            WHERE famiglie_persone.persona_id = persone.id
            AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            AND famiglie.data_creazione = persone_stati.data_inizio
        )
        ORDER BY persone.id";

    public $connection = 'db_nomadelfia';

    /**
     * Deletes the stato sposato: a sposato/a is the capofamiglia or the moglie of a famiglia of tipo coniugi,
     * also when the coniuge lives outside Nomadelfia (see PersonaInNomadelfia::statoCivile()).
     * Refuses when the date of a stato sposato is not stored as the data_creazione of such a famiglia.
     *
     * @throws RuntimeException listing the people to fix; nothing is changed
     */
    public function up(): void
    {
        $problemi = $this->sposatiSenzaFamigliaConiugi();
        if ($problemi !== []) {
            throw new RuntimeException("Migrazione interrotta, nessun dato è stato modificato. La data dello stato sposato non è la data di creazione di una loro famiglia di coniugi: correggi a mano queste persone e rilancia:\n".implode("\n", $problemi));
        }

        parent::up();
    }

    /**
     * @return list<string> one line per stato sposato whose date is not in a famiglia of tipo coniugi, with the person and the date
     */
    public function sposatiSenzaFamigliaConiugi(): array
    {
        return array_map(
            fn (object $persona): string => "- {$persona->nominativo} (id {$persona->id}): sposato/a dal {$persona->data_inizio}",
            DB::connection($this->connection)->select(self::SPOSATI_SENZA_FAMIGLIA_CONIUGI),
        );
    }
}
