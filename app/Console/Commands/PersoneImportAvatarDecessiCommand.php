<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Anagrafe\Persona\Actions\ImportAvatarDecessiAction;
use Illuminate\Console\Command;
use RuntimeException;

final class PersoneImportAvatarDecessiCommand extends Command
{
    protected $signature = 'persone:import-avatar-decessi
                            {file? : Path of the CSV of the suggestions, reviewed by hand}
                            {--suggest= : Write the CSV of the suggested persona of each photo to this path, without saving}
                            {--dir=decessi : Folder of the photos on the media_originals disk, with one subfolder per month (DECESSI1 GENNAIO …)}
                            {--dry-run : Check the file and show what would be imported, without saving}
                            {--force : Replace the avatar of the persone that already have one}';

    protected $description = 'Import the photos of the deceased, one folder per month of the decesso, as the avatar of the persone';

    public function handle(ImportAvatarDecessiAction $action): int
    {
        if (! extension_loaded('gd')) {
            $this->error('The PHP extension gd is missing: enable `extension=gd` in php.ini.');

            return self::FAILURE;
        }
        if (! extension_loaded('exif')) {
            $this->warn('The PHP extension exif is missing: the photos are not rotated as the camera recorded them. Enable `extension=exif` in php.ini.');
        }
        // a photo of the camera (up to 5760x3840) takes more than 100 MB once decoded, twice when rotated
        ini_set('memory_limit', '1024M');

        $suggerimenti = $this->option('suggest');
        if (is_string($suggerimenti) && $suggerimenti !== '') {
            return $this->suggerisci($action, $suggerimenti);
        }

        return $this->importa($action);
    }

    private function suggerisci(ImportAvatarDecessiAction $action, string $file): int
    {
        $cartella = $this->option('dir');
        $cartella = is_string($cartella) && $cartella !== '' ? $cartella : 'decessi';
        $righe = $action->suggerimenti($cartella);
        if ($righe === []) {
            $this->error("No photo found in the folder `{$cartella}` of the media_originals disk.");

            return self::FAILURE;
        }

        $handle = fopen($file, 'w');
        if ($handle === false) {
            $this->error("Unable to write {$file}");

            return self::FAILURE;
        }
        fputcsv($handle, ImportAvatarDecessiAction::COLONNE_SUGGERIMENTI, escape: '');
        foreach ($righe as $riga) {
            fputcsv($handle, array_values($riga), escape: '');
        }
        fclose($handle);

        $esiti = array_count_values(array_column($righe, 'esito'));
        $this->table(['Esito', 'Foto'], collect($esiti)->map(fn (int $foto, string $esito): array => [$esito, $foto])->values()->all());
        $this->info("Suggestions written to {$file}: review the persona_id and set the esito to OK for the rows to import, then run the command with the file.");

        return self::SUCCESS;
    }

    private function importa(ImportAvatarDecessiAction $action): int
    {
        $file = $this->argument('file');
        if (! is_string($file) || ! is_file($file) || ! is_readable($file)) {
            $this->error('File not found: '.(is_string($file) ? $file : '').'. Pass the CSV to import, or --suggest=<file> to write the suggestions.');

            return self::FAILURE;
        }

        try {
            $righe = $this->righe($file);
            $risultato = $action->execute($righe, salva: ! $this->option('dry-run'), sovrascrivi: (bool) $this->option('force'));
        } catch (RuntimeException $runtimeException) {
            $this->error($runtimeException->getMessage());

            return self::FAILURE;
        }

        $this->table(['Avatar importati', 'Righe ignorate'], [array_values($risultato)]);
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
        $mancanti = array_diff(ImportAvatarDecessiAction::COLONNE, $intestazione);
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
