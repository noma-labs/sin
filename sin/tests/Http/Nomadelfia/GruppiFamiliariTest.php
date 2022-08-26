<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Tests\TestCase;

class GruppiFamiliariTest extends TestCase
{

    /** @test */
    public function can_list_gruppi_familiari()
    {
        $this->login();
        $this->get(action([GruppifamiliariController::class, 'view']))
            ->assertSuccessful();
    }

    public function can_show_gruppo_familiari()
    {
        $g = GruppoFamiliare::all()->random();
        $this->login();
        $this->get(action([GruppifamiliariController::class, 'show']))
            ->assertSuccessful()
            ->assertSee($g->nome);
    }
}