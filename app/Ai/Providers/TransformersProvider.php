<?php

declare(strict_types=1);

namespace App\Ai\Providers;

use App\Ai\Gateway\TransformersEmbeddingGateway;
use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Ai\Contracts\Gateway\EmbeddingGateway;
use Laravel\Ai\Contracts\Providers\EmbeddingProvider;
use Laravel\Ai\Providers\Concerns\GeneratesEmbeddings;
use Laravel\Ai\Providers\Concerns\HasEmbeddingGateway;
use Laravel\Ai\Providers\Provider;

final class TransformersProvider extends Provider implements EmbeddingProvider
{
    use GeneratesEmbeddings;
    use HasEmbeddingGateway;

    public function __construct(
        protected array $config,
        protected Dispatcher $events,
    ) {}

    public function embeddingGateway(): EmbeddingGateway
    {
        return $this->embeddingGateway ??= new TransformersEmbeddingGateway;
    }

    public function defaultEmbeddingsModel(): string
    {
        return $this->config['models']['embeddings']['default'] ?? 'Xenova/all-MiniLM-L6-v2';
    }

    public function defaultEmbeddingsDimensions(): int
    {
        return $this->config['models']['embeddings']['dimensions'] ?? 384;
    }
}
