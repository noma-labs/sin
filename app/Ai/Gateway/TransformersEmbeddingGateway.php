<?php

declare(strict_types=1);

namespace App\Ai\Gateway;

use Codewithkyrian\Transformers\Pipelines\Pipeline;
use InvalidArgumentException;
use Laravel\Ai\Contracts\Gateway\EmbeddingGateway;
use Laravel\Ai\Contracts\Providers\EmbeddingProvider;
use Laravel\Ai\Providers\Provider;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Usage;
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
        $texts = array_map(static function (mixed $input): string {
            if (! is_string($input)) {
                throw new InvalidArgumentException('The Transformers embedding gateway only supports string inputs.');
            }

            return $input;
        }, $inputs);

        $pipe = $this->pipeline ??= pipeline('embeddings', $model);

        /** @var array<int, array<float>> $embeddings */
        $embeddings = $pipe($texts, pooling: 'mean', normalize: true);

        return new EmbeddingsResponse(
            embeddings: $embeddings,
            usage: new Usage, // no token usage to track since Transformers runs locally
            meta: new Meta($provider instanceof Provider ? $provider->name() : null, $model),
        );
    }
}
