<?php

namespace Tests\Biblioteca\Feature;

it('can render the homepage', function (): void {
    $this
        ->get('/biblioteca')
        ->assertSee('Libri');
});
