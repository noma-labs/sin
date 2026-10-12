<?php

declare(strict_types=1);

use App\Ai\Gateway\TransformersEmbeddingGateway;
use Laravel\Ai\AiManager;
use Laravel\Ai\Files\Document;

it('rejects non-string inputs since the local transformers pipeline only embeds text', function (): void {
    $provider = app(AiManager::class)->embeddingProvider('transformers');
    $inputs = [Document::fromString('Trascrizione di prova', 'text/plain')];

    expect(fn () => (new TransformersEmbeddingGateway)->generateEmbeddings($provider, 'Xenova/all-MiniLM-L6-v2', $inputs, 384))
        ->toThrow(InvalidArgumentException::class, 'The Transformers embedding gateway only supports string inputs.');
});
