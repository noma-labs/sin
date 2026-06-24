<?php

declare(strict_types=1);

namespace App\Ai\Gateway;

use Codewithkyrian\Transformers\Pipelines\Pipeline;
use Laravel\Ai\Contracts\Gateway\EmbeddingGateway;
use Laravel\Ai\Contracts\Providers\EmbeddingProvider;
use Laravel\Ai\Providers\Provider;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\EmbeddingsResponse;

use function Codewithkyrian\Transformers\Pipelines\pipeline;

final class TransformersEmbeddingGateway implements EmbeddingGateway
{
    private ?Pipeline $pipeline = null;

    public function generateEmbeddings(
        EmbeddingProvider $provider,
        string $model,
        array $inputs,
        int $dimensions,
        int $timeout = 30,
        array $providerOptions = [],
    ): EmbeddingsResponse {
        $pipe = $this->pipeline ??= pipeline('embeddings', $model);

        /** @var array<int, array<float>> $embeddings */
        $embeddings = $pipe($inputs, pooling: 'mean', normalize: true);

        return new EmbeddingsResponse(
            embeddings: $embeddings,
            tokens: 0, // token count (for billing/tracking); we pass 0 since Transformers runs locally
            meta: new Meta($provider instanceof Provider ? $provider->name() : null, $model),
        );
    }
}
