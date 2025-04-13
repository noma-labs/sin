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

    public $options = [];

    public $selectedCicloOption = [];

    public $selectedClassiOption = [];

    public Anno $selectedAnnoScolastico;

    public function mount(?string $scolastico): void
    {
        $this->anni = Anno::select('id', 'scolastico')->orderBy('scolastico', 'asc')->get();
        if ($scolastico) {
            $this->selectedAnnoScolastico = Anno::with('classi', 'classi.tipo')->where('scolastico', $scolastico)->first();
            $this->selectAnnoScolastico($this->selectedAnnoScolastico->id);
        }
    }

    public function render()
    {
        return view('livewire.filter-alunno');
    }

    public function selectAnnoScolastico(int $id): void
    {
        $this->selectedAnnoScolastico = Anno::with('classi', 'classi.tipo')->findorFail($id);

        $options = [];
        foreach ($this->selectedAnnoScolastico->classi as $classe) {
            $id = $classe->tipo->id;
            if (array_key_exists($id, $options) === false) {
                $options[$id] = $classe->tipo->nome;
            }
        }

        $this->options = $options;

        $this->students = Studente::InAnnoScolastico($this->selectedAnnoScolastico->id)
            ->select('persone.id', 'nominativo', 'db_scuola.tipo.ciclo', 'db_scuola.tipo.nome')
            ->get();
    }

    public function updatedSelectedCicloOption($value): void
    {
        if ($this->selectedAnnoScolastico) {
            $this->students = Studente::InAnnoScolastico($this->selectedAnnoScolastico->id)
                ->whereIn('db_scuola.tipo.ciclo', $this->selectedCicloOption)
                ->select('persone.id', 'nominativo', 'db_scuola.tipo.ciclo', 'db_scuola.tipo.nome')
                ->get();
        }
    }

    public function updatedselectedClassiOption($value): void
    {

        if ($this->selectedAnnoScolastico) {
            $query = Studente::InAnnoScolastico($this->selectedAnnoScolastico->id)
                ->whereIn('db_scuola.tipo.id', $this->selectedClassiOption)
                ->select('persone.id', 'nominativo', 'db_scuola.tipo.ciclo', 'db_scuola.tipo.nome');
            if ($this->selectedCicloOption) {
                $query->whereIn('db_scuola.tipo.ciclo', $this->selectedCicloOption);
            }
            $this->students = $query->get();
        }
    }

    public function toggleSelectAll($value): void
    {
        if ($value === 'on') {
            $this->selectedClassiOption = [];
            $this->selectedCicloOption = [];
            $this->students = Studente::InAnnoScolastico($this->selectedAnnoScolastico->id)
                ->select('persone.id', 'nominativo', 'db_scuola.tipo.ciclo', 'db_scuola.tipo.nome')
                ->get();
        }
    }
}
