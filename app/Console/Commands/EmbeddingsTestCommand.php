<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Ai\Embeddings;

final class EmbeddingsTestCommand extends Command
{
    protected $signature = 'embeddings:test {text* : One or more texts to embed}
                            {--provider=transformers : AI provider to use}';

    protected $description = 'Generate embeddings for one or more texts and display the result';

    public function handle(): int
    {
        $rawTexts = $this->argument('text');
        $texts = is_array($rawTexts) ? array_values(array_filter($rawTexts, is_string(...))) : [];

        $rawProvider = $this->option('provider');
        $provider = is_string($rawProvider) ? $rawProvider : 'transformers';

        $this->info("Provider: {$provider}");
        $this->info('Generating embeddings for '.count($texts).' input(s)...');

        $response = Embeddings::for($texts)->generate($provider);

        foreach ($response->embeddings as $i => $embedding) {
            $dimensions = count($embedding);
            $preview = implode(', ', array_map(fn (float $v) => round($v, 4), array_slice($embedding, 0, 5)));

            $this->line("  [{$i}] \"{$texts[$i]}\"");
            $this->line("       dimensions: {$dimensions} | first 5 values: [{$preview}, ...]");
        }

        $this->newLine();
        $this->info("Model: {$response->meta->model}");

        return self::SUCCESS;
    }
}
