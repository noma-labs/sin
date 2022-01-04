<?php

namespace Tests\Feature;

use Tests\TestCase;

class SinIndexTest extends TestCase
{
    /** @test */
    public function unauthenticated_user_dont_see_all_systems()
    {
        // laravel will not wrap the error in 500 http response, but return the raw error
        $this->withExceptionHandling();

        $this
            ->get('/home')
            ->assertSuccessful()
            ->assertSee("Autenticati")
            ->assertSee("Entra come ospite")
            ->assertSee("Biblioteca")   //  unauthenticated users can only search books
            ->assertDontSee("Gestione Nomadelfia")
            ->assertDontSee("Officina")
            ->assertDontSee("Amministratore")
            ->assertDontSee("Agraria")
            ->assertDontSee("Gestione Scuola")
            ->assertDontSee("Patenti");
    }
}
