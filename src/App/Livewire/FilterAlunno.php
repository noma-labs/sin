<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\DataTransferObjects\AnnoScolasticoData;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use Livewire\Component;


final class FilterAlunno extends Component
{

    public $anni = [];

    public $students = [];

    public $selectedCicloOption = [];

    public $selectedClassiOption = [];

    public Anno $selectedAnnoScolastico;

    public $selectedStudents = [];

    public $classiOptions = [];


    public AnnoScolasticoData $selectedAnnoScolasticoData;

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
        $this->selectedAnnoScolastico = Anno::with('classi', 'classi.tipo')->findorFail($id);

        $this->selectedAnnoScolasticoData = AnnoScolasticoData::FromDatabase($this->selectedAnnoScolastico);
        dd($this->selectedAnnoScolasticoData);

        $this->classiOptions = $this->selectedAnnoScolastico->classi->pluck('id', 'nome');
        dd($this->classiOptions);
        $this->students = Studente::InAnnoScolastico($this->selectedAnnoScolastico->id)->select('persone.id', 'nominativo', 'db_scuola.tipo.ciclo')->get();
    }

    public function updatedSelectedCicloOption($value)
    {
        $this->students = Studente::InAnnoScolastico($this->selectedAnnoScolastico->id)
                ->whereIn('db_scuola.tipo.ciclo', $this->selectedCicloOption)
                ->select('persone.id', 'nominativo', 'db_scuola.tipo.ciclo')->get();
    }

}
