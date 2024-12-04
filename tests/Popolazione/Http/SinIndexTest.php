<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

it('unauthenticated_user_dont_see_all_systems', function (): void {
    // laravel will not wrap the error in 500 http response, but return the raw error
    $this->withExceptionHandling();

    $this
        ->get('/home')
        ->assertSuccessful()
        ->assertSee('Autenticati')
        ->assertSee('Entra come ospite')
        ->assertSee('Biblioteca')   //  unauthenticated users can only search books
        ->assertDontSee('Gestione Nomadelfia')
        ->assertDontSee('Officina')
        ->assertDontSee('Amministratore')
        ->assertDontSee('Agraria')
        ->assertDontSee('Gestione Scuola')
        ->assertDontSee('Patenti');
});
