<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Archive\Models\RecordingTranscript;
use App\Archive\Models\TranscriptChunk;
use Exception;
use Illuminate\Console\Command;

final class TranscriptsChunkCommand extends Command
{
    protected $signature = 'transcripts:chunk
                            {--limit=5 : Number of transcripts to process}
                            {--truncate : Truncate the chunks table before processing}';

    protected $description = 'Split recording transcripts into text chunks';

    public function handle(): int
    {
        $this->info('Chunking transcripts...');
        $this->newLine();

        try {
            $limit = (int) $this->option('limit');
            $truncate = (bool) $this->option('truncate');

            if ($truncate) {
                TranscriptChunk::query()->truncate();
                $this->info('Chunks table truncated.');
            }

            /** @var \Illuminate\Database\Eloquent\Builder<RecordingTranscript> $query */
            $query = RecordingTranscript::query()
                ->whereNotNull('content')
                ->where('content', '!=', '')
                ->whereDoesntHave('chunks');

            /** @var \Illuminate\Database\Eloquent\Collection<int, RecordingTranscript> $transcripts */
            $transcripts = $query->limit($limit)->get();

            if ($transcripts->isEmpty()) {
                $this->warn('No transcripts to process.');

                return self::FAILURE;
            }

            $this->info("Found {$transcripts->count()} transcripts to chunk");
            $this->newLine();

            foreach ($transcripts as $transcript) {
                $this->info("Processing transcript ID {$transcript->id}: {$transcript->heading}");

                $chunks = $this->recursiveChunk($transcript->content, 1200);
                $this->info('Chunks: '.count($chunks));

                foreach ($chunks as $index => $chunk) {
                    $this->line('  chunk '.($index + 1).'/'.count($chunks).' ('.mb_strlen($chunk).' chars)');
                    TranscriptChunk::query()->create([
                        'recording_transcript_id' => $transcript->id,
                        'chunk_index' => $index,
                        'content' => $chunk,
                    ]);
                }

                $this->line('<fg=green>✓</> Saved '.count($chunks).' chunks');
                $this->newLine();
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
