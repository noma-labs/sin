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
                            {--limit=5 : Number of transcripts to process}';

    protected $description = 'Create embeddings for recording transcripts using the Transformers pipeline';

    public function handle(): int
    {
        $this->info('Creating embeddings for transcripts...');
        $this->newLine();

        try {
            $limit = (int) $this->option('limit');

            $this->info('Loading embeddings model: Xenova/all-MiniLM-L6-v2');
            $extractor = pipeline('embeddings', 'Xenova/all-MiniLM-L6-v2');

            $transcripts = RecordingTranscript::whereNotNull('content')
                ->where('content', '!=', '')
                ->limit($limit)
                ->get();

            if ($transcripts->isEmpty()) {
                $this->warn('No recording transcripts with content found in the database.');

                return self::FAILURE;
            }

            $this->info("Found {$transcripts->count()} transcripts");
            $this->newLine();

            foreach ($transcripts as $transcript) {
                $this->info("Processing transcript ID {$transcript->id}: {$transcript->heading}");
                $this->info('Content length: '.strlen($transcript->content).' characters');

                $this->line($transcript->content);
                $chunks = $this->chunk($transcript->content, 600);
                foreach ($chunks as $index => $chunk) {
                    $this->info("Processing chunk ".($index + 1)."/".count($chunks)." (".strlen($chunk)." characters)");
                    // $embeddings = $extractor(
                    //     $chunk,
                    //     normalize: true,
                    //     pooling: 'mean'
                    // );

                    $this->line('<fg=green>✓</> Embeddings generated successfully!');
                    $this->newLine();
                    // $this->info('Embeddings length: '.count($embeddings[0])); // 384
                    // $this->line(json_encode($embeddings[0], JSON_PRETTY_PRINT));
                    // $this->newLine();
                }

                // $embeddings = $extractor(
                //     $transcript->content,
                //     normalize: true,
                //     pooling: 'mean'
                // );

                // $this->line('<fg=green>✓</> Embeddings generated successfully!');
                // $this->newLine();
                // $this->info('Embeddings length: '.count($embeddings[0])); // 384
                // $this->line(json_encode($embeddings[0], JSON_PRETTY_PRINT));
                // $this->newLine();
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
}
