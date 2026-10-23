<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SqlMigrations\SqlMigration;

final class DeriveTipoFamiglia extends SqlMigration
{
    /**
     * The checks that the stati mamma di vocazione, nomadelfa mamma and mamma nubile give one tipo to one famiglia.
     *
     * @var array<string, string>
     */
    private const array CONTROLLI = [
        "ha uno stato mamma attivo, ma nella sua famiglia c'è il coniuge (capofamiglia o moglie)" => "
            SELECT DISTINCT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAM', 'MAN')
            JOIN persone ON persone.id = persone_stati.persona_id
            JOIN famiglie_persone ON famiglie_persone.persona_id = persone.id
                AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            JOIN famiglie_persone coniuge ON coniuge.famiglia_id = famiglie_persone.famiglia_id
                AND coniuge.persona_id <> persone.id
                AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
            WHERE persone_stati.stato = '1'",
        'ha più stati mamma attivi diversi: lascia attivo solo quello giusto' => "
            SELECT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAM', 'MAN')
            JOIN persone ON persone.id = persone_stati.persona_id
            WHERE persone_stati.stato = '1'
            GROUP BY persone.id, persone.nominativo
            HAVING COUNT(DISTINCT stati.stato) > 1",
        'non ha stati mamma attivi, ma ha più stati mamma chiusi diversi: scegli il tipo della famiglia' => "
            SELECT persone.id, persone.nominativo
            FROM persone_stati
            JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAM', 'MAN')
            JOIN persone ON persone.id = persone_stati.persona_id
            WHERE NOT EXISTS (
                SELECT 1 FROM persone_stati attivo
                JOIN stati stati_attivi ON stati_attivi.id = attivo.stato_id AND stati_attivi.stato IN ('MAV', 'MAM', 'MAN')
                WHERE attivo.persona_id = persone.id AND attivo.stato = '1'
            )
            GROUP BY persone.id, persone.nominativo
            HAVING COUNT(DISTINCT stati.stato) > 1",
        'ha uno stato mamma ed è capofamiglia di più famiglie: scegli la famiglia' => "
            SELECT persone.id, persone.nominativo
            FROM famiglie_persone
            JOIN persone ON persone.id = famiglie_persone.persona_id
            WHERE famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
            AND EXISTS (
                SELECT 1 FROM persone_stati
                JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAM', 'MAN')
                WHERE persone_stati.persona_id = persone.id
            )
            GROUP BY persone.id, persone.nominativo
            HAVING COUNT(DISTINCT famiglie_persone.famiglia_id) > 1",
    ];

    /**
     * The famiglie with a capofamiglia or a moglie whose tipo cannot be derived.
     */
    private const string FAMIGLIE_SENZA_TIPO = "
        SELECT famiglie.id, famiglie.nome_famiglia
        FROM famiglie
        WHERE famiglie.tipo IS NULL
        AND EXISTS (
            SELECT 1 FROM famiglie_persone
            WHERE famiglie_persone.famiglia_id = famiglie.id AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        )
        ORDER BY famiglie.id";

    public $connection = 'db_nomadelfia';

    /**
     * Sets the tipo of the famiglie that have none, from the coniugi in the famiglia and the stati of the capofamiglia.
     * Refuses when a stato mamma does not give one tipo to one famiglia: those people must be fixed by hand before migrating.
     * The famiglie whose tipo cannot be derived are left empty and logged, to be set by hand.
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

        $famiglieSenzaTipo = $this->famiglieSenzaTipo();
        if ($famiglieSenzaTipo !== []) {
            Log::warning("Tipo non ricavabile, impostalo a mano dalla pagina della famiglia:\n".implode("\n", $famiglieSenzaTipo));
        }
    }

    /**
     * @return list<string> one line per person whose stati mamma do not give one tipo to one famiglia
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
     * @return list<string> one line per famiglia left without tipo
     */
    public function famiglieSenzaTipo(): array
    {
        return array_map(
            fn (object $famiglia): string => "- {$famiglia->nome_famiglia} (id {$famiglia->id})",
            DB::connection($this->connection)->select(self::FAMIGLIE_SENZA_TIPO),
        );
    }
}
