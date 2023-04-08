<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;

it('can_list_gruppi_familiari', function () {
    login();
    $this->get(action([GruppifamiliariController::class, 'view']))
        ->assertSuccessful();
});

it('can_show_gruppo_familiari', function () {
    $g = GruppoFamiliare::all()->random();
    login();
    $this->get(action([GruppifamiliariController::class, 'edit'], ['id' => $g->id]))
        ->assertSuccessful()
        ->assertSee($g->nome);
});
