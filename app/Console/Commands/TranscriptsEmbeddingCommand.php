<?php

declare(strict_types=1);

namespace App\Console\Commands;

use function Codewithkyrian\Transformers\Pipelines\pipeline;
use App\Archive\Models\RecordingTranscript;
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
                $query->whereNull('embedding');
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

                $chunks = $this->chunk($transcript->content, 600);
                $this->info('Chunks: '.count($chunks));

                $chunkEmbeddings = [];
                foreach ($chunks as $index => $chunk) {
                    $this->line("  chunk ".($index + 1)."/".count($chunks)." (".mb_strlen($chunk)." chars)");
                    $result = $extractor($chunk, normalize: true, pooling: 'mean');
                    $chunkEmbeddings[] = $result[0];
                }

                $embedding = count($chunkEmbeddings) === 1
                    ? $chunkEmbeddings[0]
                    : $this->averageEmbeddings($chunkEmbeddings);

                $transcript->embedding = $embedding;
                $transcript->save();

                $this->line('<fg=green>✓</> Saved embedding ('.count($embedding).' dims)');
                $this->newLine();
            }

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    public function chunk(string $text, int $maxChars = 600): array
    {
        $paragraphs = preg_split('/\n\s*\n/', trim($text));
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            if ($current !== '' && mb_strlen($current) + mb_strlen($paragraph) + 2 > $maxChars) {
                $chunks[] = $current;
                $current = '';
            }

            $current = $current === '' ? $paragraph : $current."\n\n".$paragraph;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    /**
     * @param  array<array<float>>  $embeddings
     * @return array<float>
     */
    private function averageEmbeddings(array $embeddings): array
    {
        $dims = count($embeddings[0]);
        $avg = array_fill(0, $dims, 0.0);

        foreach ($embeddings as $vec) {
            for ($i = 0; $i < $dims; $i++) {
                $avg[$i] += $vec[$i];
            }
        }

        $n = count($embeddings);
        $norm = 0.0;
        for ($i = 0; $i < $dims; $i++) {
            $avg[$i] /= $n;
            $norm += $avg[$i] * $avg[$i];
        }

        // Re-normalize after averaging
        $norm = sqrt($norm);
        if ($norm > 0.0) {
            for ($i = 0; $i < $dims; $i++) {
                $avg[$i] /= $norm;
            }
        }

        return $avg;
    }
}
