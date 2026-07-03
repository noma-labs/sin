<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Archive\Models\RecordingTranscript;
use App\Archive\Models\TranscriptChunk;
use App\Archive\UniversityAlbum;
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
                /** @var \Illuminate\Database\Eloquent\Collection<int, TranscriptChunk> $chunks */
                $chunks = $transcript->chunks()->orderBy('chunk_index')->get();

                $contents = $chunks->pluck('content')->toArray();
                $response = Embeddings::for($contents)->generate('transformers');

                $upsertRows = $chunks->map(fn (TranscriptChunk $chunk, int $i) => [
                    'recording_transcript_id' => $chunk->recording_transcript_id,
                    'chunk_index' => $chunk->chunk_index,
                    'content' => $chunk->content,
                    'embedding' => json_encode($response->embeddings[$i]),
                ])->all();

                TranscriptChunk::upsert($upsertRows, ['recording_transcript_id', 'chunk_index'], ['embedding']);

                $this->line("<fg=green>✓</> {$transcript->heading} — ".count($contents).' embeddings');
            }

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
