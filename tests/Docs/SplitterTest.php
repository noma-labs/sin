<?php

declare(strict_types=1);

namespace Tests\Docs;

use App\Http\RecursiveCharacterTextSplitter;

it('forbids access to guests', function (): void {
    $this->assertTrue(true);

    $splitter = new RecursiveCharacterTextSplitter();
    $content  = <<<'EOD'
Lorem ipsum dolor sit amet, consectetur adipiscing elit.

Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
EOD;
    $lines  = $splitter->splitText($content);
    dd($lines);

})->only();
