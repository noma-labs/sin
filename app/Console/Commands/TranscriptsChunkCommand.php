<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Archive\UniversityAlbum;
use App\Archive\Models\RecordingTranscript;
use App\Archive\Models\TranscriptChunk;
use Exception;
use Illuminate\Console\Command;

final class TranscriptsChunkCommand extends Command
{
    protected $signature = 'transcripts:chunk';

    protected $description = 'Split recording transcripts into text chunks';

    public function handle(): int
    {
        $this->info('Chunking transcripts...');
        $this->newLine();

        try {
            $transcripts = RecordingTranscript::query()
                ->whereIn('code', UniversityAlbum::CODES)
                ->whereNotNull('content')
                ->where('content', '!=', '')
                ->get();


            if ($transcripts->isEmpty()) {
                $this->warn('No transcripts to process.');

                return self::FAILURE;
            }

            $this->info("Found {$transcripts->count()} transcripts to chunk");

            if (! $this->confirm('This will truncate the chunks table. Continue?')) {
                return self::FAILURE;
            }

            TranscriptChunk::query()->truncate();
            $this->info('Chunks table truncated.');

            foreach ($transcripts as $transcript) {
                $chunks = $this->recursiveChunk($transcript->content, 1200);

                foreach ($chunks as $index => $chunk) {
                    TranscriptChunk::query()->create([
                        'recording_transcript_id' => $transcript->id,
                        'chunk_index' => $index,
                        'content' => $chunk,
                    ]);
                }

                $this->line("<fg=green>✓</> {$transcript->heading} — ".count($chunks).' chunks');
            }

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @param  string[]  $separators
     * @return string[]
     */
    private function recursiveChunk(?string $text, int $maxChars, array $separators = ["\n\n", "\n", ' ', '']): array
    {
        if ($text === '' || $text === null || mb_strlen($text) <= $maxChars) {
            return $text !== '' && $text !== null ? [$text] : [];
        }

        $separator = '';
        $nextSeparators = [];
        foreach ($separators as $i => $sep) {
            if ($sep === '' || str_contains($text, $sep)) {
                $separator = $sep;
                $nextSeparators = array_slice($separators, $i + 1);
                break;
            }
        }

        $pieces = $separator !== '' ? explode($separator, $text) : mb_str_split($text);

        $chunks = [];
        $current = '';

        foreach ($pieces as $piece) {
            if ($piece === '') {
                continue;
            }

            $joined = $current === '' ? $piece : $current.$separator.$piece;

            if (mb_strlen($joined) <= $maxChars) {
                $current = $joined;
            } else {
                if ($current !== '') {
                    $chunks[] = $current;
                    $current = '';
                }

                if (mb_strlen($piece) > $maxChars) {
                    foreach ($this->recursiveChunk($piece, $maxChars, $nextSeparators) as $sub) {
                        $chunks[] = $sub;
                    }
                } else {
                    $current = $piece;
                }
            }
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }
}
