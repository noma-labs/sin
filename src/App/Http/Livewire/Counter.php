<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public $name = 'Faieie';

    public $annoSearch = 'Faieie';

    public $studente = 'Studente';

    public $classe = 'Classe';

    public function increment()
    {
        $this->count++;
    }

    public function search()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
