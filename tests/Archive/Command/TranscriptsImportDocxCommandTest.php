<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('transcripts_originals');
});

it('imports docx transcript preserving paragraph boundaries', function (): void {
    $fixtureName = '1949registrazioni.docx';
    $fixturePath = __DIR__.DIRECTORY_SEPARATOR.$fixtureName;
    $goldenPath = __DIR__.DIRECTORY_SEPARATOR.'1949registrazioni.golden.json';

    Storage::disk('transcripts_originals')->put($fixtureName, (string) file_get_contents($fixturePath));

    $this->artisan('transcripts:import-docx', ['file' => $fixtureName])->assertSuccessful();

    $imported = DB::connection('archivio_nomadelfia')
        ->table('recording_transcripts')
        ->where('file_path', $fixtureName)
        ->orderBy('id')
        ->get(['heading', 'content'])
        ->map(fn (object $row): array => [
            'heading' => (string) $row->heading,
            'content' => (string) $row->content,
        ])
        ->all();

    $expected = json_decode((string) file_get_contents($goldenPath), true, flags: JSON_THROW_ON_ERROR);

    expect($imported)
        ->toHaveCount(count($expected))
        ->toBe($expected);
})->only();
