<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Wireable;

final class Option implements Wireable
{
    public function __construct(public int|string $id, public string $value) {}

    public static function fromLivewire($value)
    {
        $id = $value['id'];
        $value = $value['value'];

        return new self($id, $value);
    }

    public function toLivewire()
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
        ];
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

    public bool $multiple = true;

    /**
     * Abstract method that must be implemented by the child class.
     *
     * @return Option[]
     */
    abstract public function searchBy(string $term): array;

    /**
     * Abstract method that must be implemented by the child class.
     *
     * @param  int[]  $ids  Array of integer IDs.
     * @return Option[]
     */
    abstract public function selected(array $ids): array;

    final public function mount(array|int $persone_id = [], string $placeholder = '--- Inserisci  ---', string $name_input = 'persone_id[]', bool $multiple = false): void
    {
        $this->placeholder = $placeholder;
        $this->nameInput = $name_input;
        $this->multiple = $multiple;

        if (is_array($persone_id)) {
            $this->selected = $this->selected($persone_id);
        } else {
            $this->selected = $this->selected([$persone_id]);
        }

    }

    final public function select(int|string $id): void
    {
        $found = collect($this->options)->first(fn(Option $opt): bool => (string) $opt->id === (string) $id);

        if ($this->multiple) {
            $this->selected = collect($this->selected)->push($found)->unique();
        } else {
            $this->selected = [$found];
        }

        $this->dispatch('option-selected', $id);
        $this->reset('options', 'searchTerm');
    }

    final public function deselect(int|string $id): void
    {
        $this->selected = collect($this->selected)->reject(fn(Option $selected): bool => (string) $selected->id === (string) $id);
    }

    final public function updatedSearchTerm(string $value): void
    {
        $this->search($value);
    }

    final public function search(string $term): void
    {
        $this->reset('options');

        $this->options = $this->searchBy($term);
    }

    final public function render()
    {
        return view('livewire.autocomplete');
    }
}
