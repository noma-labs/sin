<?php

declare(strict_types=1);

namespace App;

final class DocumentChunk
{
    /**
     * @param  string[]  $content
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $description,
        public readonly array $content,
    ) {}

    /**
     * @return string[]
     */
    public function getAllLines(): array
    {
        return array_merge([$this->description], $this->content);
    }
}
