<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Wireable;

class Option implements Wireable
{
    public function __construct(public int $id, public string $value) { }

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

abstract class Autocomplete extends Component
{
    public string $searchTerm;

    public string $placeholder;

    public $options = [];

    public $selected = [];

    public string $noResultsMessage = 'Nessun risultato trovato';

    public string $nameInput = 'persone_id[]';

    /**
     * Abstract method that must be implemented by the child class.
     *
     * @param string $term
     * @return Option[]
     */
    abstract public function searchBy(string $term): array;


     /**
     * Abstract method that must be implemented by the child class.
     *
     * @param []int ids
     * @return Option[]
     */
    abstract public function selected(array $ids): array;


    public function mount(array $persone_id = [], string $placeholder = '-- Inserisci --', string $name_input = 'persone_id[]'): void
    {
        $this->placeholder = $placeholder;
        $this->nameInput = $name_input;

        if (! empty($persone_id)) {
            $this->selected = $this->selected($persone_id);
        }
    }

    public function select(string $id): void
    {
        $found = collect($this->options)->first(function (Option $opt) use ($id): bool {
            return $opt->id == $id;
        });

        $this->selected = collect($this->selected)->push($found)->unique();
        $this->reset('options', 'searchTerm');
    }

    public function deselect(string $id): void
    {
        $this->selected = collect($this->selected)->reject(function (Option $selected) use ($id): bool {
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

        $this->options =  $this->searchBy($term);
    }


    public function render()
    {
        return view('livewire.search-persona');
    }



}