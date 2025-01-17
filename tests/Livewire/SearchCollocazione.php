<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Livewire\SearchCollocazione;
use Livewire\Livewire;

it('can render succesfully the component', function (): void {
    Livewire::test(SearchCollocazione::class)->assertStatus(200);
});

it('can search collocazione', function (): void {
    Livewire::test(SearchCollocazione::class)
        ->call('search', 'AAA')
        ->assertSee('AAA');
});
