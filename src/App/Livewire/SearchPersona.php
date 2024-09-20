<?php

namespace App\Livewire;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Wireable;

class Option implements Wireable
{
    public function __construct(public int $id, public string $value)
    {
    }

    public function toLivewire()
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
        ];
    }

    public static function fromLivewire($value)
    {
        $id = $value['id'];
        $value = $value['value'];

        return new self($id, $value);
    }
}

class SearchPersona extends Component
{
    public string $searchTerm;

    public string $placeholder;

    public $options = [];

    public Collection $selected;

    public string $noResultsMessage = 'Nessun risultato trovato';

    public string $nameInput = 'persone_id[]';

    public function mount(array $persone_id = [], string $placeholder = '--Inserisci Nominativo--', string $name_input = 'persone_id[]'): void
    {
        $this->placeholder = $placeholder;
        $this->selected = collect();
        $this->nameInput = $name_input;

        if (! empty($persone_id)) {
            $persone = Persona::query()
                ->select('persone.id', 'persone.nominativo', 'persone.nome', 'persone.cognome', 'persone.data_nascita')
                ->whereIn('id', $persone_id)
                ->orderBy('nominativo', 'asc')
                ->get();

            foreach ($persone as $persona) {
                $this->selected[] = new Option($persona->id, $persona->nominativo.' ('.$persona->data_nascita.')');
            }
        }
    }

    public function select(string $id): void
    {
        $found = collect($this->options)->first(function (Option $opt) use ($id): bool {
            return $opt->id == $id;
        });

        $this->selected = $this->selected->push($found)->unique();
        $this->reset('options', 'searchTerm');
    }

    public function deselect(string $id): void
    {
        $this->selected = $this->selected->reject(function (Option $selected) use ($id): bool {
            return $selected->id == $id;
        });
    }

    public function updatedSearchTerm(string $value): void
    {
        $this->search($value);
    }

    public function search(string $term): void
    {
        $this->reset('options');
        $persone = Persona::query()
            ->select('persone.id', 'persone.nominativo', 'persone.nome', 'persone.cognome', 'persone.data_nascita')
            ->where('nominativo', 'LIKE', "$term%")
            ->orderBy('nominativo', 'asc')
            ->get();

        foreach ($persone as $persona) {
            $this->options[] = new Option($persona->id, $persona->nominativo.' ('.$persona->data_nascita.')');
        }

    }

    public function render()
    {
        return view('livewire.search-persona');
    }
}
