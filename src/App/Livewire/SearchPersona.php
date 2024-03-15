<?php

namespace App\Livewire;

use Domain\Nomadelfia\Persona\Models\Persona;
use Livewire\Component;

class SearchPersona extends Component
{
    public $searchTerm = '';
    public $placehodler = '<inserisci nominativo>';

    public $people = [];


    public function mount()
    {
  // get the data from db and set the propoerties
    }

    public function render()
    {
        return view('livewire.search-persona');
    }


    public function updatedSearchTerm($value)
    {
        $this->reset('people');
        $term = $value;
        $persone = Persona::where('nominativo', 'LIKE', "$term%")
                    ->orderBy('nominativo')
                    ->get();
        foreach ($persone as $persona) {
            $this->people[] = ['id' => $persona->id, 'nominativo' => $persona->nominativo];
        }
    }
}
