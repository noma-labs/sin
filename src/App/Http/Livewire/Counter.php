<?php

namespace App\Http\Livewire;

use App\Officina\Models\ViewClienti;
use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

//    public $placeholder = 'Inserisci Nominativo';

    public $term = '--insert-name--';

//    public function increment()
//    {
//        $this->count++;
//    }

    public function search()
    {
//        $term = $request->input('term');
//        $clienti = ViewClienti::where('nominativo', 'LIKE', $term.'%')->orderBy('nominativo')->take(50)->get();
//        $results = [];
//        foreach ($clienti as $persona) {
//            $resul
//        $this->count++;
    }

    public function mount()

    {

        $this->term = '---Inserisci Nominativo---';

    }

    public function render()
    {
        return view('livewire.counter');
    }
}
