<?php

namespace App\Livewire;

use App\Officina\Models\ViewClienti;
use Livewire\Component;

class SearchPersona extends Component
{
    public string $searchTerm;

    public string $placeholder;

    public $people = [];

    public ViewClienti $selected;

    public string $inputName = 'nome';

    public string $noResultsMessage = 'Nessun risultato trovato';

    public function mount(string $placeholder = '--Inserisci Nominativo--')
    {
        $this->placeholder = $placeholder;

        if (old($this->inputName) != null) {
            $this->selected = ViewClienti::query()->findOrFail(old($this->inputName));
        }
    }

    public function render()
    {
        return view('livewire.search-persona');
    }

    public function updatedSearchTerm($value)
    {

        if (strlen($value) < 2) { // start searching only of the teram is more than 2 char
            $this->noResultsMessage = "--Inserisci almeno 2 caratteri--";
            return $this->reset('people');
        }
        $this->search($value);
    }

    public function search(string $term)
    {
        $this->reset('people');
        $this->people = ViewClienti::query()->where('nominativo', 'LIKE', "$term%")->orderBy('nominativo', 'asc')->get();
    }

    public function select($personID)
    {
        $this->selected = ViewClienti::query()->find($personID);
        $this->reset('people');
    }

    public function clear()
    {
        $this->reset('searchTerm', 'selected', 'people');
    }
}
