<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Actions;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Imports the famiglie and their componenti rebuilt from the FAMIGLIA text of the file ALFA di Enrico (alfa_enrico_15_feb_23),
 * from the CSV of the dry run reviewed by hand. Only the famiglie and their componenti are written, without dates of entrata or uscita:
 * the rows are history (stato 0), so the current nuclei are not changed.
 */
final class ImportFamiglieStoricheAction
{
    /**
     * The esiti of the rows to import: the other ones (GIA_PRESENTE, DA_VERIFICARE, CONFLITTO, SOLO_NEL_DB) are skipped.
     */
    public const ESITI_DA_IMPORTARE = ['DA_AGGIUNGERE', 'NUOVA_FAMIGLIA', 'OK'];

    /**
     * The columns of the CSV read by the import.
     */
    public const COLONNE = ['esito', 'persona_id', 'testo_famiglia', 'famiglia', 'famiglia_id_db', 'nome_famiglia', 'tipo', 'data_creazione', 'posizione', 'stato'];

    private const POSIZIONI = ['CAPO FAMIGLIA', 'MOGLIE', 'FIGLIO NATO', 'FIGLIO ACCOLTO', 'SINGLE'];

    /**
     * Imports the rows in one transaction; with $salva false everything is rolled back, to see what would be imported.
     *
     * @param  list<array<string, string>>  $righe  the rows of the CSV, by column name
     * @return array{famiglie_create: int, componenti_aggiunti: int, righe_ignorate: int}
     *
     * @throws RuntimeException listing the problems of the file; nothing is changed
     */
    public function execute(array $righe, bool $salva = true): array
    {
        $problemi = $this->problemi($righe);
        if ($problemi !== []) {
            throw new RuntimeException("Importazione interrotta, nessun dato è stato modificato. Correggi il file e rilancia:\n".implode("\n", $problemi));
        }

        $daImportare = array_filter($righe, $this->isDaImportare(...));
        $connection = DB::connection('db_nomadelfia');
        $connection->beginTransaction();
        try {
            $famigliaId = [];
            foreach ($daImportare as $riga) {
                $chiave = $riga['famiglia'];
                if ($riga['famiglia_id_db'] !== '' || isset($famigliaId[$chiave])) {
                    continue;
                }
                $famigliaId[$chiave] = Famiglia::query()->create([
                    'nome_famiglia' => $riga['nome_famiglia'],
                    'tipo' => TipoFamiglia::from($riga['tipo']),
                    'data_creazione' => $riga['data_creazione'] ?: null,
                ])->id;
            }

            $componenti = array_map(fn (array $riga): array => [
                'famiglia_id' => $riga['famiglia_id_db'] !== '' ? (int) $riga['famiglia_id_db'] : $famigliaId[$riga['famiglia']],
                'persona_id' => (int) $riga['persona_id'],
                'posizione_famiglia' => $riga['posizione'],
                'stato' => $riga['stato'],
                'note' => mb_substr('Dal file ALFA di Enrico: '.$riga['testo_famiglia'], 0, 500),
            ], array_values($daImportare));
            foreach (array_chunk($componenti, 500) as $blocco) {
                $connection->table('famiglie_persone')->insert($blocco);
            }

            $salva ? $connection->commit() : $connection->rollBack();
        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw $throwable;
        }

        return [
            'famiglie_create' => count($famigliaId),
            'componenti_aggiunti' => count($componenti),
            'righe_ignorate' => count($righe) - count($daImportare),
        ];
    }

    /**
     * @param  list<array<string, string>>  $righe
     * @return list<string> one line per problem, with the line of the CSV (the header is line 1)
     */
    public function problemi(array $righe): array
    {
        $problemi = [];
        $daImportare = array_filter($righe, $this->isDaImportare(...));
        $connection = DB::connection('db_nomadelfia');
        $persone = $connection->table('persone')
            ->whereIn('id', array_map(fn (array $riga): int => (int) $riga['persona_id'], $daImportare))
            ->pluck('id')->flip();
        $famiglieEsistenti = $connection->table('famiglie')->pluck('id')->flip();
        $nomiEsistenti = [];
        foreach ($connection->table('famiglie')->pluck('nome_famiglia') as $nome) {
            if (is_string($nome)) {
                $nomiEsistenti[mb_strtolower(mb_trim($nome))] = true;
            }
        }
        $componentiEsistenti = $connection->table('famiglie_persone')
            ->whereIn('persona_id', $persone->keys())
            ->get(['famiglia_id', 'persona_id'])
            ->mapWithKeys(fn (object $componente): array => ["{$componente->famiglia_id}-{$componente->persona_id}" => true]);

        $nuoveFamiglie = [];
        $componenti = [];
        foreach ($daImportare as $indice => $riga) {
            $linea = 'riga '.($indice + 2);
            if (! ctype_digit($riga['persona_id']) || ! $persone->has((int) $riga['persona_id'])) {
                $problemi[] = "- {$linea}: la persona `{$riga['persona_id']}` non esiste";
            }
            if (! in_array($riga['posizione'], self::POSIZIONI, true)) {
                $problemi[] = "- {$linea}: la posizione `{$riga['posizione']}` non è valida";
            }
            if (! in_array($riga['stato'], ['0', '1'], true)) {
                $problemi[] = "- {$linea}: lo stato `{$riga['stato']}` non è valido, usa 0 (storico) o 1 (nel nucleo)";
            }

            if ($riga['famiglia_id_db'] !== '') {
                if (! ctype_digit($riga['famiglia_id_db']) || ! $famiglieEsistenti->has((int) $riga['famiglia_id_db'])) {
                    $problemi[] = "- {$linea}: la famiglia `{$riga['famiglia_id_db']}` non esiste";
                }
                $chiaveFamiglia = 'db-'.$riga['famiglia_id_db'];
            } else {
                $chiaveFamiglia = 'nuova-'.$riga['famiglia'];
                $famiglia = [$riga['nome_famiglia'], $riga['tipo'], $riga['data_creazione']];
                if (isset($nuoveFamiglie[$riga['famiglia']]) && $nuoveFamiglie[$riga['famiglia']] !== $famiglia) {
                    $problemi[] = "- {$linea}: la famiglia `{$riga['famiglia']}` ha nome, tipo o data_creazione diversi dalle righe precedenti";
                }
                $nuoveFamiglie[$riga['famiglia']] ??= $famiglia;
                if (mb_trim($riga['nome_famiglia']) === '') {
                    $problemi[] = "- {$linea}: manca il nome_famiglia della nuova famiglia `{$riga['famiglia']}`";
                } elseif (isset($nomiEsistenti[mb_strtolower(mb_trim($riga['nome_famiglia']))])) {
                    $problemi[] = "- {$linea}: esiste già una famiglia di nome `{$riga['nome_famiglia']}`";
                }
                if (! TipoFamiglia::tryFrom($riga['tipo']) instanceof TipoFamiglia) {
                    $problemi[] = "- {$linea}: il tipo `{$riga['tipo']}` non è valido";
                }
                if (! $this->isData($riga['data_creazione'])) {
                    $problemi[] = "- {$linea}: data_creazione `{$riga['data_creazione']}` non è una data AAAA-MM-GG";
                }
            }

            if ($riga['famiglia_id_db'] !== '' && $componentiEsistenti->has("{$riga['famiglia_id_db']}-{$riga['persona_id']}")) {
                $problemi[] = "- {$linea}: la persona {$riga['persona_id']} è già nella famiglia {$riga['famiglia_id_db']}";
            }
            if (isset($componenti[$chiaveFamiglia][$riga['persona_id']])) {
                $problemi[] = "- {$linea}: la persona {$riga['persona_id']} è già in una riga precedente della stessa famiglia";
            }
            $componenti[$chiaveFamiglia][$riga['persona_id']] = $riga['posizione'];
        }

        $nomi = array_map(fn (array $famiglia): string => mb_strtolower(mb_trim($famiglia[0])), $nuoveFamiglie);
        foreach (array_diff_assoc($nomi, array_unique($nomi)) as $chiave => $nome) {
            $problemi[] = "- la nuova famiglia `{$chiave}` ha lo stesso nome_famiglia `{$nuoveFamiglie[$chiave][0]}` di un'altra nuova famiglia";
        }
        foreach ($componenti as $chiaveFamiglia => $posizioni) {
            if (! str_starts_with((string) $chiaveFamiglia, 'nuova-')) {
                continue;
            }
            $nome = mb_substr((string) $chiaveFamiglia, 6);
            if (! in_array('CAPO FAMIGLIA', $posizioni, true)) {
                $problemi[] = "- la nuova famiglia `{$nome}` non ha un CAPO FAMIGLIA";
            }
            foreach (['CAPO FAMIGLIA', 'MOGLIE'] as $posizione) {
                if (count(array_keys($posizioni, $posizione, true)) > 1) {
                    $problemi[] = "- la nuova famiglia `{$nome}` ha più di un {$posizione}";
                }
            }
        }

        return $problemi;
    }

    /**
     * @param  array<string, string>  $riga
     */
    private function isDaImportare(array $riga): bool
    {
        return in_array($riga['esito'], self::ESITI_DA_IMPORTARE, true);
    }

    private function isData(string $valore): bool
    {
        if ($valore === '') {
            return true;
        }
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $valore);

        return $data !== false && $data->format('Y-m-d') === $valore;
    }
}
