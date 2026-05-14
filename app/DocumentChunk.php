<?php

declare(strict_types=1);

namespace App;

final readonly class DocumentChunk
{
    /**
     * @param  string[]  $content
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public array $content,
    ) {}

    /**
     * @return string[]
     */
    public function getAllLines(): array
    {
        return array_merge([$this->description], $this->content);
    }
}
