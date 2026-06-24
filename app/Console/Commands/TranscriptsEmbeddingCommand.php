<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Archive\Models\RecordingTranscript;
use App\Archive\Models\TranscriptChunk;
use Exception;
use Illuminate\Console\Command;
use Laravel\Ai\Embeddings;

final class TranscriptsEmbeddingCommand extends Command
{
    protected $signature = 'transcripts:embedding
                            {--limit=5 : Number of transcripts to process}
                            {--force : Re-generate embeddings even if they already exist}';

    protected $description = 'Create embeddings for transcript chunks using the Transformers pipeline';

    public function handle(): int
    {
        $this->info('Creating embeddings for transcript chunks...');
        $this->newLine();

        try {
            $limit = (int) $this->option('limit');
            $force = (bool) $this->option('force');

            /** @var \Illuminate\Database\Eloquent\Builder<RecordingTranscript> $query */
            $query = RecordingTranscript::query()->has('chunks');

            if (! $force) {
                $query->whereHas('chunks', fn ($q) => $q->whereNull('embedding'));
            }

            /** @var \Illuminate\Database\Eloquent\Collection<int, RecordingTranscript> $transcripts */
            $transcripts = $query->limit($limit)->get();

            if ($transcripts->isEmpty()) {
                $this->warn('No transcripts to process (use --force to re-generate existing embeddings).');

                return self::FAILURE;
            }

            $this->info("Found {$transcripts->count()} transcripts to embed");
            $this->newLine();

            foreach ($transcripts as $transcript) {
                $this->info("Processing transcript ID {$transcript->id}: {$transcript->heading}");

                /** @var \Illuminate\Database\Eloquent\Collection<int, TranscriptChunk> $chunks */
                $chunks = $transcript->chunks()
                    ->when(! $force, fn ($q) => $q->whereNull('embedding'))
                    ->get();

                $contents = $chunks->pluck('content')->toArray();
                $response = Embeddings::for($contents)->generate('transformers');

                foreach ($chunks as $index => $chunk) {
                    $this->line('  chunk '.($index + 1).'/'.count($contents).' ('.mb_strlen($chunk->content).' chars)');
                    $chunk->update(['embedding' => $response->embeddings[$index]]);
                }

                $this->line('<fg=green>✓</> Saved '.count($contents).' embeddings');
                $this->newLine();
            }

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
