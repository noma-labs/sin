<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Anagrafe\Famiglia\Actions\ImportFamiglieStoricheAction;
use Illuminate\Console\Command;
use RuntimeException;

final class FamiglieImportAlfaCommand extends Command
{
    protected $signature = 'famiglie:import-alfa
                            {file : Path of the CSV of the dry run, reviewed by hand}
                            {--dry-run : Check the file and show what would be imported, without saving}';

    protected $description = 'Import the famiglie and their componenti rebuilt from the FAMIGLIA text of alfa_enrico_15_feb_23';

    public function handle(ImportFamiglieStoricheAction $action): int
    {
        $file = $this->argument('file');
        if (! is_string($file) || ! is_file($file) || ! is_readable($file)) {
            $this->error('File not found: '.(is_string($file) ? $file : ''));

            return self::FAILURE;
        }

        try {
            $righe = $this->righe($file);
            $risultato = $action->execute($righe, salva: ! $this->option('dry-run'));
        } catch (RuntimeException $runtimeException) {
            $this->error($runtimeException->getMessage());

            return self::FAILURE;
        }

        $this->table(['Famiglie create', 'Componenti aggiunti', 'Righe ignorate'], [array_values($risultato)]);
        $this->info($this->option('dry-run') ? 'Dry run: nothing has been saved.' : 'Import complete.');

        return self::SUCCESS;
    }

    /**
     * @return list<array<string, string>>
     */
    private function righe(string $file): array
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            throw new RuntimeException("Unable to open {$file}");
        }
        $intestazione = $this->valori(fgetcsv($handle, escape: ''));
        $mancanti = array_diff(ImportFamiglieStoricheAction::COLONNE, $intestazione);
        if ($mancanti !== []) {
            fclose($handle);
            throw new RuntimeException('Missing columns in the CSV: '.implode(', ', $mancanti));
        }

        $righe = [];
        while (($valori = fgetcsv($handle, escape: '')) !== false) {
            if ($valori === [null]) {
                continue;
            }
            $valori = array_slice(array_pad($this->valori($valori), count($intestazione), ''), 0, count($intestazione));
            $righe[] = array_combine($intestazione, $valori);
        }
        fclose($handle);

        return $righe;
    }

    /**
     * @param  list<string|null>|false  $valori  a line read by fgetcsv
     * @return list<string> the trimmed values, empty for a missing value
     */
    private function valori(array|false $valori): array
    {
        return $valori === false ? [] : array_map(fn (?string $valore): string => mb_trim($valore ?? ''), $valori);
    }
}
