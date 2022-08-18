<?php

namespace App\Http\Livewire;

use App\Nomadelfia\Models\PopolazioneNomadelfia;
use Livewire\Component;

class Dashboard extends Component
{
    public $search = "ad";

    public function render()
    {
//        dd($this->search);
        $popolazione = PopolazioneNomadelfia::presente()->where("nominativo", 'like', "%".$this->search."%")->get();
        return view('livewire.dashboard', ['popolazione' => $popolazione]);
    }
}
