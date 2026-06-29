<?php

declare(strict_types=1);

namespace Tests\Archive\Http;

use App\Archive\Models\Recording;
use App\Archive\Models\RecordingAudio;
use App\Archive\Models\RecordingTranscript;

use function Pest\Laravel\get;

it('shows archive problems counters for missing transcript and mp3', function (): void {
    $withEverything = Recording::query()->create([
        'DATA' => '1980-01-01',
        'AUTORE' => 'Saltini Don Zeno',
        'ARGOMENTO' => 'Con trascrizione e mp3',
    ]);

    $missingTranscript = Recording::query()->create([
        'DATA' => '1980-01-02',
        'AUTORE' => 'Saltini Don Zeno',
        'ARGOMENTO' => 'Senza trascrizione',
    ]);

    $missingMp3 = Recording::query()->create([
        'DATA' => '1980-01-03',
        'AUTORE' => 'Saltini Don Zeno',
        'ARGOMENTO' => 'Manca audio',
    ]);

    RecordingTranscript::query()->create([
        'recording_id' => $withEverything->id,
        'heading' => 'Trascrizione 1',
        'file_path' => 'transcripts/one.txt',
        'content' => 'contenuto',
    ]);

    RecordingAudio::query()->create([
        'recording_id' => $withEverything->id,
        'file_name' => 'one.mp3',
        'file_path' => 'audio/one.mp3',
        'file_size_bytes' => 100,
    ]);

    RecordingTranscript::query()->create([
        'recording_id' => $missingMp3->id,
        'heading' => 'Trascrizione 2',
        'file_path' => 'transcripts/two.txt',
        'content' => 'contenuto',
    ]);

    RecordingAudio::query()->create([
        'recording_id' => $missingTranscript->id,
        'file_name' => 'two.mp3',
        'file_path' => 'audio/two.mp3',
        'file_size_bytes' => 200,
    ]);

    login();

    get(route('archive.index'))
        ->assertSuccessful()
        ->assertViewHas('missingTranscriptCount', 1)
        ->assertViewHas('missingMp3Count', 1)
        ->assertSee('Problemi archivio')
        ->assertSee('Senza trascrizione')
        ->assertSee('Senza MP3')
        ->assertSee('⚠ Senza MP3');
});
