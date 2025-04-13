<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;
use Livewire\Component;

final class EntrataPersona extends Component
{
    public Persona $persona;

    public string $dataEntrata;

    public string $tipologia;

    public bool $showFamigliaSelect = false;

    public bool $showGruppoFamiliareSelect = false;

    final public function mount(Persona $persona): void
    {
        $this->persona = $persona;
        $this->dataEntrata = Carbon::now()->toDateString();
    }

    final public function updatedTipologia(string $value): void
    {
        $this->showFamigliaSelect = $value === 'dalla_nascita' || $value === 'minorenne_accolto' || $value === 'minorenne_famiglia';
        $this->showGruppoFamiliareSelect = $value === 'maggiorenne_single' || $value === 'maggiorenne_famiglia';

        if ($value === 'dalla_nascita' && $this->persona->data_nascita) {
            $this->dataEntrata = Carbon::parse($this->persona->data_nascita)->toDateString();
        } else {
            $this->dataEntrata = Carbon::now()->toDateString();
        }
    }

    final public function render()
    {
        return view('livewire.entrata-persona');
    }
}
