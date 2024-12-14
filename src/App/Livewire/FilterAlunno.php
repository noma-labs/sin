<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use Livewire\Component;


final class FilterAlunno extends Component
{

    public $anni = [];

    public $students = [];

    public $selectedOption;

    public $classiOption  = [];

    public function mount(): void
    {
        $this->anni = Anno::select('id','scolastico')->orderBy('scolastico', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.filter-alunno');
    }

    public function selectAnnoScolastico(int $id)
    {
        $this->students = Studente::InAnnoScolastico($id)->select('persone.id', 'nominativo')->get();
        $this->classiOption = Anno::find($id)->classi()->with('tipo')->get();
    }

    public function updatedSelectedOption($value)
    {
        // Handle the change event for selected option
        // You can update the $students property or perform other actions based on the selected option

        dd($value);
    }

}
