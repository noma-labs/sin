<?php

declare(strict_types=1);

use App\Agraria\Models\Manutenzione;
use App\Agraria\Models\ManutenzioneProgrammata;
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

it('updates a manutenzione', function () {
    // Arrange
    $mezzo = MezzoAgricolo::factory()->create();
    $programmata = ManutenzioneProgrammata::first();
    $manutenzione = Manutenzione::factory()->create([
        'mezzo_agricolo' => $mezzo->id,
        'data' => '2024-01-01',
        'ore' => 100,
        'spesa' => 50,
        'persona' => 'Mario',
        'lavori_extra' => 'Cambio olio',
    ]);
    $manutenzione->programmate()->attach($programmata->id);

    login();

    // Act
    $response = $this->put(route('agraria.maintenanace.update', $manutenzione->id), [
        'mezzo' => $mezzo->id,
        'data' => '2024-02-02',
        'ore' => 200,
        'spesa' => 99.99,
        'persona' => 'Luigi',
        'straordinarie' => 'Filtro aria',
        'programmate' => [$programmata->id],
    ]);

    // Assert
    $response->assertRedirect(route('agraria.maintenanace.show', $manutenzione->id));
    $manutenzione->refresh();
    expect($manutenzione->data)->toBe('2024-02-02');
    expect($manutenzione->ore)->toBe(200);
    expect($manutenzione->spesa)->toBe(99.99);
    expect($manutenzione->persona)->toBe('Luigi');
    expect($manutenzione->lavori_extra)->toBe('Filtro aria');
    expect($manutenzione->programmate->contains($programmata->id))->toBeTrue();
});
