<?php

namespace App\Livewire;

use Livewire\Component;

class PrenotazioneVeicoli extends Component
{
    public $veicoli = [];

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.prenotazioni-veicoli');
    }
}
