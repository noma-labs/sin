<?php

declare(strict_types=1);

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;

final class AggiungiElaborato extends Component
{
    public string $titolo;

    public string $annoScolastico;

    public array $classi;

    public function mount(): void
    {
        $this->titolo = '';
        $this->annoScolastico = Carbon::now()->year.'/'.(Carbon::now()->year + 1);
        $this->classi = ['elementari', '1 elementare', '2 elementare', '3 elementare', '4 elementare', '5 elementare', '1 media', '2 media', '3 media', '1 superiore', '2 superiore', '3 superiore', '4 superiore', '5 superiore'];
    }

    public function save(): void
    {
        dd('save');
    }

    public function render()
    {
        return view('livewire.aggiungi-elaborato');
    }
}
