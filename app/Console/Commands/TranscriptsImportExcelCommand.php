<?php

declare(strict_types=1);

namespace App\Console\Commands;

use DateTimeImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

final class TranscriptsImportExcelCommand extends Command
{
    protected $signature = 'transcripts:import-xlsx
                            {file? : XLSX filename from transcripts_originals disk}
                            {--chunk=500 : Number of rows per insert query}
                            {--truncate : Truncate table before import}
                            {--dry-run : Parse file without inserting rows}';

    protected $description = 'Import transcript rows from transcripts_originals XLSX into archivio_nomadelfia.recordings';

    public function handle(): int
    {
        $disk = Storage::disk('transcripts_originals');
        $file = (string) ($this->argument('file') ?? '2026-05-10 REGISTR.xlsx');

        if (! $disk->exists($file)) {
            $this->error("XLSX file not found in transcripts_originals disk: {$file}");

            return self::FAILURE;
        }

        $filePath = $disk->path($file);

        $chunkSize = (int) $this->option('chunk');
        if ($chunkSize <= 0) {
            $this->error('Option --chunk must be a positive integer.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $connection = DB::connection('archivio_nomadelfia');

        if ((bool) $this->option('truncate') && ! $dryRun) {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');
            $connection->table('recordings')->truncate();
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('Table recordings truncated.');
        }

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = (int) $sheet->getHighestDataRow('A');
        $rows = $sheet->rangeToArray("A1:AF{$highestRow}", null, true, true, false);

        if ($rows === []) {
            $this->warn('No rows found in XLSX file.');

            return self::SUCCESS;
        }

        $headers = null;
        $batch = [];
        $insertedRows = 0;
        $processedRows = 0;
        $skippedRows = 0;

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            if ($headers === null) {
                $headers = $this->normalizeHeaders($row);

                continue;
            }

            $processedRows++;

            $row = $this->normalizeRowLength($row, count($headers));

            if ($this->isEmptyRow($row)) {
                $skippedRows++;

                continue;
            }

            $payload = [];
            foreach ($headers as $index => $header) {
                $payload[$header] = $this->normalizeValue($header, $row[$index] ?? null);
            }

            $batch[] = $payload;

            if (count($batch) >= $chunkSize) {
                $insertedRows += $this->flushBatch($connection, $batch, $dryRun);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $insertedRows += $this->flushBatch($connection, $batch, $dryRun);
        }

        $mode = $dryRun ? 'dry-run parsed' : 'inserted';
        $this->info("{$mode} rows: {$insertedRows}");
        $this->line("processed rows: {$processedRows}");
        $this->line("skipped empty rows: {$skippedRows}");

        return self::SUCCESS;
    }

    /**
     * @return list<mixed>
     */
    private function normalizeRowLength(array $row, int $headersCount): array
    {
        $row = array_slice($row, 0, $headersCount);

        if (count($row) < $headersCount) {
            $row = array_pad($row, $headersCount, null);
        }

        return $row;
    }

    /**
     * @return list<string>
     */
    private function normalizeHeaders(array $headers): array
    {
        $columnMappings = [
            "LOCALITA'" => 'LOCALITA',
            'ORIG.' => 'ORIG',
            'MIN-PAG.' => 'MIN-PAG',
            'TRASCRITT.' => 'TRASCRITT',
        ];

        return array_map(
            static fn ($header): string => $columnMappings[(string) $header] ?? (string) $header,
            $headers
        );
    }

    /**
     * @param  list<string|null>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeValue(string $header, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($header === 'DATA') {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            if (is_string($value)) {
                $value = mb_trim($value);

                if ($value === '') {
                    return null;
                }

                try {
                    $date = DateTimeImmutable::createFromFormat('d/m/Y', $value) ?: DateTimeImmutable::createFromFormat('Y-m-d', $value);

                    if ($date) {
                        return $date->format('Y-m-d');
                    }
                } catch (Throwable) {
                    // Fall through to return original value
                }
            }
        }

        if (is_string($value)) {
            $value = mb_trim($value);

            return $value === '' ? null : $value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        return $value;
    }

    /**
     * @param  array<int, array<string, mixed>>  $batch
     */
    private function flushBatch(object $connection, array $batch, bool $dryRun): int
    {
        if ($batch === []) {
            return 0;
        }

        if (! $dryRun) {
            $connection->table('recordings')->insert($batch);
        }

        return count($batch);
    }
}
