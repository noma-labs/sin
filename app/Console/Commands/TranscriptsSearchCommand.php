<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Archive\Models\TranscriptChunk;
use Exception;
use Illuminate\Console\Command;

use function Codewithkyrian\Transformers\Pipelines\pipeline;

final class TranscriptsSearchCommand extends Command
{
    protected $signature = 'transcripts:search
                            {query : The search query}
                            {--top=5 : Number of top results to return}';

    protected $description = 'Search transcripts by semantic similarity using PHP dot product (no DB extensions needed)';

    public function handle(): int
    {
        $query = $this->argument('query');
        if (! is_string($query)) {
            $this->error('Missing query argument.');

            return self::FAILURE;
        }
        $top = (int) $this->option('top');

        try {
            $this->info("Loading model and embedding query: \"{$query}\"");
            $extractor = pipeline('embeddings', 'Xenova/all-MiniLM-L6-v2');
            /** @var array<int, float[]> $result */
            $result = $extractor($query, normalize: true, pooling: 'mean');
            $queryEmbedding = $result[0];

            $chunks = TranscriptChunk::query()->with('transcript')->whereNotNull('embedding')->get();

            if ($chunks->isEmpty()) {
                $this->warn('No chunks with embeddings found. Run transcripts:embedding first.');

                return self::FAILURE;
            }

            $this->info("Comparing query against {$chunks->count()} chunks...");
            $this->newLine();

            $scores = [];
            $bestChunks = [];
            foreach ($chunks as $chunk) {
                $transcriptId = $chunk->recording_transcript_id;
                $score = $this->dotProduct($queryEmbedding, $chunk->embedding);
                if (! isset($scores[$transcriptId]) || $score > $scores[$transcriptId]) {
                    $scores[$transcriptId] = $score;
                    $bestChunks[$transcriptId] = $chunk;
                }
            }

            arsort($scores);
            $topIds = array_slice(array_keys($scores), 0, $top, true);

            $rows = [];
            foreach ($topIds as $transcriptId) {
                $chunk = $bestChunks[$transcriptId];
                $transcript = $chunk->transcript;
                $rows[] = [
                    $transcript->id,
                    $transcript->code ?? '-',
                    mb_strimwidth($transcript->heading ?? '-', 0, 60, '…'),
                    number_format($scores[$transcriptId], 4),
                    mb_strimwidth((string) $chunk->content, 0, 80, '…'),
                ];
            }

            $this->table(['ID', 'Code', 'Heading', 'Score', 'Chunk'], $rows);

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Dot product equals cosine similarity when both vectors are normalized.
     *
     * @param  array<float>  $a
     * @param  array<float>  $b
     */
    private function dotProduct(array $a, array $b): float
    {
        $sum = 0.0;
        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $sum += $a[$i] * $b[$i];
        }

        return $sum;
    }
}
