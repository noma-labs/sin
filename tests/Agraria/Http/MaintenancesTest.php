<?php

declare(strict_types=1);

use App\Agraria\Models\MezzoAgricolo;
use App\Agraria\Models\StoricoOre;

it('open the maintenance page', function (): void {
    login();

    $this->get(route('agraria.maintenanace.search.show'))
        ->assertSuccessful()
        ->assertSee('Manutenzione');
});

it('saves historic hours when a new maintenance is added to a vehicle', function (): void {
    $m = new MezzoAgricolo();
    $m->nome = 'test';
    $m->numero_telaio = 'test';
    $m->tot_ore = 10;
    $m->save();

    login();

    $this->post(route('agraria.maintenanace.create'), [
        'mezzo' => $m->id,
        'data' => '2021-01-01',
        'ore' => 20,
        'persona' => 'test',
        'straordinarie' => 'test',
    ])->assertSessionHasNoErrors();

    expect(MezzoAgricolo::findOrFail($m->id)->tot_ore)->toBe(20);
    expect(StoricoOre::where('mezzo_agricolo', $m->id)->orderBy('data', 'DESC')->first()->ore)->toBe(10);
});
