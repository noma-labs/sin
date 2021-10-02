<?php

namespace Tests\Feature;

use Tests\TestCase;

class SinIndexTest extends TestCase
{
    /** @test */
    public function index_show_title()
    {
        // laravel will not wrap the error in 500 http response, but return the raw error
        $this->withExceptionHandling();

        $this
            ->get('/')
            ->assertSuccessful()
            ->assertSee("Autenticati")
            ->assertSee("Entra come ospite");
    }
}
