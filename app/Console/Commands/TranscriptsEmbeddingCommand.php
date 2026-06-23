<?php

declare(strict_types=1);

namespace App\Console\Commands;

use function Codewithkyrian\Transformers\Pipelines\pipeline;
use App\Archive\Models\RecordingTranscript;
use App\Archive\Models\TranscriptChunk;
use Exception;
use Illuminate\Console\Command;

final class TranscriptsEmbeddingCommand extends Command
{
    protected $signature = 'transcripts:embedding
                            {--limit=5 : Number of transcripts to process}
                            {--force : Re-generate embeddings even if they already exist}';

    protected $description = 'Create embeddings for recording transcripts using the Transformers pipeline';

    public function handle(): int
    {
        $this->info('Creating embeddings for transcripts...');
        $this->newLine();

        try {
            $limit = (int) $this->option('limit');
            $force = (bool) $this->option('force');

            $this->info('Loading embeddings model: Xenova/all-MiniLM-L6-v2');
            $extractor = pipeline('embeddings', 'Xenova/all-MiniLM-L6-v2');

            $query = RecordingTranscript::whereNotNull('content')
                ->where('content', '!=', '');

            if (! $force) {
                $query->whereDoesntHave('chunks');
            }

            $transcripts = $query->limit($limit)->get();

            if ($transcripts->isEmpty()) {
                $this->warn('No transcripts to process (use --force to re-generate existing embeddings).');

                return self::FAILURE;
            }

            $this->info("Found {$transcripts->count()} transcripts to embed");
            $this->newLine();

            foreach ($transcripts as $transcript) {
                $this->info("Processing transcript ID {$transcript->id}: {$transcript->heading}");

                $chunks = $this->recursiveChunk($transcript->content, 1200);
                $this->info('Chunks: '.count($chunks));

                $transcript->chunks()->delete();

                foreach ($chunks as $index => $chunk) {
                    $this->line("  chunk ".($index + 1)."/".count($chunks)." (".mb_strlen($chunk)." chars)");
                    $result = $extractor($chunk, normalize: true, pooling: 'mean');
                    TranscriptChunk::create([
                        'recording_transcript_id' => $transcript->id,
                        'chunk_index' => $index,
                        'content' => $chunk,
                        'embedding' => $result[0],
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
    private function recursiveChunk(string $text, int $maxChars, array $separators = ["\n\n", "\n", " ", ""]): array
    {
        if ($text === '' || mb_strlen($text) <= $maxChars) {
            return $text !== '' ? [$text] : [];
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
