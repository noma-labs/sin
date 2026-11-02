<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Livewire\FilterAlunno;
use Livewire\Livewire;

it('can render succesfully the component', function (): void {
    Livewire::test(FilterAlunno::class)->assertStatus(200);
});
