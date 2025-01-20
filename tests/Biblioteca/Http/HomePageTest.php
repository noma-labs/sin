<?php

declare(strict_types=1);

namespace Tests\Biblioteca\Feature;

it('guest user can see homepage', function (): void {
    $this
        ->get(route('biblioteca'))
        ->assertSee('Libri');
});
