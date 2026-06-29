<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

it('truncates recordings by default before xlsx import', function (): void {
    Storage::fake('transcripts_originals');
    $connection = DB::connection('archivio_nomadelfia');
    $columns = collect($connection->select('SHOW COLUMNS FROM recordings'))
        ->map(static fn (object $column): string => (string) $column->Field)
        ->values()
        ->all();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $row = array_fill(0, count($columns), null);
    $dataIndex = array_search('DATA', $columns, true);
    $oreIndex = array_search('ORE', $columns, true);
    if (($columns[0] ?? null) === 'id') {
        $row[0] = 1;
    }
    if ($dataIndex !== false) {
        $row[$dataIndex] = '2026-06-29';
    }
    if ($oreIndex !== false) {
        $row[$oreIndex] = '12';
    }

    $sheet->fromArray([
        $columns,
        $row,
    ]);

    $tempPath = tempnam(sys_get_temp_dir(), 'xlsx-import-');
    if ($tempPath === false) {
        throw new RuntimeException('Unable to create temporary XLSX file.');
    }

    (new Xlsx($spreadsheet))->save($tempPath);
    Storage::disk('transcripts_originals')->put('test.xlsx', file_get_contents($tempPath) ?: '');
    @unlink($tempPath);

    $connection->table('recordings')->insert([
        'DATA' => '2000-01-01',
        'ORE' => '01',
    ]);

    $this->artisan('transcripts:import-xlsx', ['file' => 'test.xlsx'])->assertSuccessful();

    $records = $connection->table('recordings')->get();

    expect($records)->toHaveCount(1);
    expect($records->first()?->DATA)->toBe('2026-06-29');
});
