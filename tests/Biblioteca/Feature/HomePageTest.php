<?php

namespace Tests\Biblioteca\Feature;

it('can render the homepage', function () {
    $this
        ->get('/biblioteca')
        ->assertSee('Libri');
});
