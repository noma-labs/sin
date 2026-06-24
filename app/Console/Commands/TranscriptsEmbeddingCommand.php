<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Archive\UniversityAlbum;
use App\Archive\Models\RecordingTranscript;
use App\Archive\Models\TranscriptChunk;
use Exception;
use Illuminate\Console\Command;
use Laravel\Ai\Embeddings;

final class TranscriptsEmbeddingCommand extends Command
{

    protected $signature = 'transcripts:embedding
                            {--limit=100 : Number of transcripts to process}';

    protected $description = 'Create embeddings for transcript chunks using the Transformers pipeline';

    public function handle(): int
    {
        $this->info('Creating embeddings for transcript chunks...');
        $this->newLine();

        try {
            $limit = (int) $this->option('limit');

            /** @var \Illuminate\Database\Eloquent\Builder<RecordingTranscript> $query */
            $query = RecordingTranscript::query()
                ->whereIn('code', UniversityAlbum::CODES)
                ->has('chunks');

            /** @var \Illuminate\Database\Eloquent\Collection<int, RecordingTranscript> $transcripts */
            $transcripts = $query->limit($limit)->get();

            if ($transcripts->isEmpty()) {
                $this->warn('No transcripts with chunks found. Run transcripts:chunk first.');

                return self::FAILURE;
            }

            $this->info("Found {$transcripts->count()} transcripts to embed");

            if (! $this->confirm('This will clear existing embeddings for the selected transcripts. Continue?')) {
                return self::FAILURE;
            }

            foreach ($transcripts as $transcript) {
                $transcript->chunks()->update(['embedding' => null]);

                /** @var \Illuminate\Database\Eloquent\Collection<int, TranscriptChunk> $chunks */
                $chunks = $transcript->chunks()->get();

                if ($chunks->isEmpty()) {
                    $this->warn("{$transcript->heading} — no chunks, run transcripts:chunk first");

                    continue;
                }

                $contents = $chunks->pluck('content')->toArray();
                $response = Embeddings::for($contents)->generate('transformers');

                foreach ($chunks as $index => $chunk) {
                    $chunk->update(['embedding' => $response->embeddings[$index]]);
                }

                $this->line("<fg=green>✓</> {$transcript->heading} — ".count($contents).' embeddings');
            }

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
