<?php

namespace App\Livewire;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Collection;
use Livewire\Component;

class SearchPersona extends Component
{
    public string $searchTerm;

    public string $placeholder;

    public $options = []; // array of persone

    public Collection $selected;

    public string $noResultsMessage = 'Nessun risultato trovato';

    public function mount(string $placeholder = '--Inserisci Nominativo--'): void
    {
        $this->placeholder = $placeholder;
        $this->selected = collect();
    }

    public function select(string $alias): void
    {
        $this->selected = $this->selected->push($alias)->unique();
        $this->reset('options', 'searchTerm');
    }

    public function deselect(string $alias): void
    {
        $this->selected = $this->selected->reject(function ($selectedAlias) use ($alias): bool {
            return $selectedAlias == $alias;
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
            ->select('persone.id', 'persone.nominativo', 'persone.nome', 'persone.cognome', 'persone.data_nascita', 'persone_alias.alias')
            ->leftjoin('db_rtn.persone_alias', 'persona_id', '=', 'id')
            ->where('nominativo', 'LIKE', "$term%")
            ->orderBy('nominativo', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.search-persona');
    }
}
