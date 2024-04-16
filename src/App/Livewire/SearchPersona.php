<?php

namespace App\Livewire;

use App\Officina\Models\ViewClienti;
use Domain\Nomadelfia\Persona\Models\Persona;
use Livewire\Component;

class SearchPersona extends Component
{
    public string $searchTerm;

    public string $placeholder;

    public $options = [];

    public $selected = []; // array of persone selected

    public string $inputName = 'nome';

    public string $noResultsMessage = 'Nessun risultato trovato';

    public function mount(string $placeholder = '--Inserisci Nominativo--')
    {
        $this->placeholder = $placeholder;

        if (old($this->inputName) != null) {
            $this->selected = Persona::query()->findOrFail(old($this->inputName));
        }
    }

    public function render()
    {
        return view('livewire.search-persona');
    }

    public function updatedSearchTerm($value)
    {

        // if (strlen($value) <= 2) { // start searching only of the term is more than 2 chars
        //     $this->noResultsMessage = '--Inserisci almeno 2 caratteri--';

        //     return $this->reset('options');
        // }
        $this->search($value);
    }

    public function search(string $term)
    {
        $this->reset('options');
        $this->options = Persona::query()->where('nominativo', 'LIKE', "$term%")->orderBy('nominativo', 'asc')->get();
    }

    public function select($personID)
    {
        // if (in_array($personID, array_map(function($person) { return $person->id; }, $this->selected))) {
        //     $this->selected = array_filter($this->selected, function ($person) use ($personID) {
        //         return $person->id != $personID;
        //     });
        // }
        $contained = collect($this->selected)->contains(function (Persona $value, int $key) use ($personID) {
            return $value->id == $personID;
        });

        if (!$contained) {
            $this->selected[] = Persona::query()->find($personID);
        }
        $this->reset('options', 'searchTerm');

    }

    public function clear()
    {
        $this->reset('searchTerm', 'selected', 'options');
    }

    public function deselect(int $personID)
    {
        $this->selected = array_filter($this->selected, function ($person) use ($personID) {
            return $person->id != $personID;
        });
    }
}
