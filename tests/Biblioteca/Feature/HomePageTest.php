<?php

declare(strict_types=1);

namespace Tests\Biblioteca\Feature;

it('can render the homepage', function (): void {
    $this
        ->get('/biblioteca')
        ->assertSee('Libri');
});
