<?php

declare(strict_types=1);

namespace App\Ai\Gateway;

use Laravel\Ai\Contracts\Gateway\EmbeddingGateway;
use Laravel\Ai\Contracts\Providers\EmbeddingProvider;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\EmbeddingsResponse;

use function Codewithkyrian\Transformers\Pipelines\pipeline;

final class TransformersEmbeddingGateway implements EmbeddingGateway
{
    /** @var array<string, FeatureExtractionPipeline> */
    private array $pipelines = [];

    public function generateEmbeddings(
        EmbeddingProvider $provider,
        string $model,
        array $inputs,
        int $dimensions,
        int $timeout = 30,
        array $providerOptions = [],
    ): EmbeddingsResponse {
        $pipe = $this->pipelines[$model] ??= pipeline('feature-extraction', $model);

        $embeddings = array_map(
            fn (string $input) => $pipe($input, pooling: 'mean', normalize: true)[0],
            $inputs,
        );

        return new EmbeddingsResponse(
            embeddings: $embeddings,
            tokens: 0,
            meta: new Meta($provider->name(), $model),
        );
    }
}
