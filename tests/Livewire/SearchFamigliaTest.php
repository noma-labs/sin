<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Livewire\SearchFamiglia;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use Livewire\Livewire;

it('can render succesfully the component', function (): void {
    Livewire::test(SearchFamiglia::class)
        ->assertStatus(200);
});

it('can search famiglia', function (): void {
    Famiglia::factory()->create(['nome_famiglia' => 'a-family']);

    Livewire::test(SearchFamiglia::class)
        ->call('search', 'a-fam')
        ->assertSee('a-family');
})->only();
