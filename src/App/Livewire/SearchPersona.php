<?php

namespace App\Livewire;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Collection;
use Livewire\Component;

class SearchPersona extends Component
{
    public string $searchTerm;

    public string $placeholder;

    public $options = [];

    public Collection $selected;

    public string $noResultsMessage = 'Nessun risultato trovato';

    public function mount(string $placeholder = '--Inserisci Nominativo--'): void
    {
        $this->placeholder = $placeholder;
        $this->selected = collect();
    }

    public function select(string $nominativo): void
    {
        $this->selected = $this->selected->push($nominativo)->unique();
        $this->reset('options', 'searchTerm');
    }

    public function deselect(string $nominativo): void
    {
        $this->selected = $this->selected->reject(function ($selected) use ($nominativo): bool {
            return $selected == $nominativo;
        });
    }

    public function clear(): void
    {
        $this->selected = collect();
        $this->reset('options', 'searchTerm');
    }

    public function updatedSearchTerm(string $value): void
    {
        $this->search($value);
    }

    public function search(string $term): void
    {
        $this->reset('options');
        $this->options = Persona::query()
            ->select('persone.id', 'persone.nominativo', 'persone.nome', 'persone.cognome', 'persone.data_nascita')
            ->where('nominativo', 'LIKE', "$term%")
            ->orderBy('nominativo', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.search-persona');
    }
}
