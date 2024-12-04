<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;

it('can_list_gruppi_familiari', function (): void {
    login();
    $this->get(action([GruppifamiliariController::class, 'view']))
        ->assertSuccessful();
});

it('can_show_gruppo_familiari', function (): void {
    $g = GruppoFamiliare::all()->random();
    login();
    $this->get(action([GruppifamiliariController::class, 'edit'], ['id' => $g->id]))
        ->assertSuccessful()
        ->assertSee($g->nome);
});
