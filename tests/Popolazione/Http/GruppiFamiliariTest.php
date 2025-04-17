<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Controllers\GruppofamiliareController;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;

it('can_list_gruppi_familiari', function (): void {
    login();
    $this->get(action([GruppofamiliareController::class, 'index']))
        ->assertSuccessful();
});

it('can_show_gruppo_familiari', function (): void {
    $g = GruppoFamiliare::all()->random();
    login();
    $this->get(action([GruppofamiliareController::class, 'show'], $g->id))
        ->assertSuccessful()
        ->assertSee($g->nome);
});
