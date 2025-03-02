<?php

declare(strict_types=1);

it('allow view the agraria home page only to logged user', function (): void {
    $this->get(route('agraria.index'))
        ->assertRedirect(route('login'));

    login();

    $this->get(route('agraria.index'))
        ->assertSuccessful()
        ->assertSee('Agraria');
});
