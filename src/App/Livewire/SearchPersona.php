<?php

namespace App\Livewire;

use Domain\Nomadelfia\Persona\Models\Persona;
use Livewire\Component;

class SearchPersona extends Component
{
    public $searchTerm = '';

    public $placehodler = '<inserisci nominativo>';

    public $people = [];

    public $selected = [];

    public function mount()
    {
        // $this->people = Persona::orderBy('nominativo')->limit(10)->get();
        // setup component: get the data from db and set the properties
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

    public function add($personID)
    {
        $this->selected[] = Persona::find($personID);
    }

    public function remove($personID)
    {
        $this->selected = array_filter($this->selected, function ($persona) use ($personID) {
            return $persona->id !== $personID;
        });
    }
}
