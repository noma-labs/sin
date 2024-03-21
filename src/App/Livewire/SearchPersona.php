<?php

namespace App\Livewire;

use Domain\Nomadelfia\Persona\Models\Persona;
use Livewire\Component;

class SearchPersona extends Component
{
    public string $searchTerm;

    public string $placeholder;

    public $people = [];

    public Persona $selected;

    public string $inputName;

    public function mount(string $name = 'persona_id', string $placeholder = '--Inserisci Nominativo--')
    {
        $this->inputName = $name;
        $this->placeholder = $placeholder ?? $this->placeholder;

        if (old($this->inputName) != null) {
            $this->selected = Persona::findOrFail(old($this->inputName));
        }
    }

    public function render()
    {
        return view('livewire.search-persona');
    }

    public function updatedSearchTerm($value)
    {
        $this->reset('people');
        $this->people = Persona::where('nominativo', 'LIKE', "$value%")->orderBy('nominativo')->get();
    }

    public function select($personID)
    {
        $this->selected = Persona::find($personID);
        $this->reset('people');
    }

    public function clear()
    {
        $this->reset('searchTerm', 'selected', 'people');
    }
}
