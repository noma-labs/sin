<?php

declare(strict_types=1);

namespace App\Livewire;

use DateTimeInterface;
use Livewire\Component;
use Livewire\Wireable;

/**
 * @phpstan-type DatiPersona array{id: int, nome: string|null, cognome: string|null, nominativo: string|null, sesso: string|null, avatar_path: string|null, updated_at: string|null}
 */
final class Option implements Wireable
{
    /**
     * @param  DatiPersona|null  $persona  the persona of the option, shown with their avatar (x-persona-avatar)
     */
    public function __construct(public int|string $id, public string $value, public ?array $persona = null) {}

    /**
     * The option of a persona, shown with their avatar: a Persona, or any row with the columns of persone it needs.
     */
    public static function perPersona(object $persona, string $value): self
    {
        $testo = static fn (mixed $valore): ?string => is_string($valore) && $valore !== '' ? $valore : null;
        $id = is_numeric($persona->id ?? null) ? (int) $persona->id : 0;
        $aggiornato = $persona->updated_at ?? null;

        return new self($id, $value, [
            'id' => $id,
            'nome' => $testo($persona->nome ?? null),
            'cognome' => $testo($persona->cognome ?? null),
            'nominativo' => $testo($persona->nominativo ?? null),
            'sesso' => $testo($persona->sesso ?? null),
            'avatar_path' => $testo($persona->avatar_path ?? null),
            'updated_at' => $aggiornato instanceof DateTimeInterface ? $aggiornato->format('Y-m-d H:i:s') : $testo($aggiornato),
        ]);
    }

    /**
     * @param  array{id: int|string, value: string, persona?: DatiPersona|null}  $value
     */
    public static function fromLivewire($value): self
    {
        return new self($value['id'], $value['value'], $value['persona'] ?? null);
    }

    /**
     * @return array{id: int|string, value: string, persona: DatiPersona|null}
     */
    public function toLivewire(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'persona' => $this->persona,
        ];
    }
}

abstract class Autocomplete extends Component
{
    public string $searchTerm = '';

    public string $placeholder;

    /** @var Option[] */
    public array $options = [];

    /** @var Option[] */
    public array $selected = [];

    public string $noResultsMessage = '-- Nessun risultato --';

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
        $found = collect($this->options)->first(fn (Option $opt): bool => (string) $opt->id === (string) $id);

        if ($found === null) {
            return;
        }

        if ($this->multiple) {
            $this->selected = collect($this->selected)->push($found)->unique('id')->values()->all();
        } else {
            $this->selected = [$found];
        }

        $this->dispatch($this->selectedEvent(), $id);
        $this->reset('options', 'searchTerm');
    }

    final public function deselect(int|string $id): void
    {
        $this->selected = collect($this->selected)->reject(fn (Option $selected): bool => (string) $selected->id === (string) $id)->values()->all();
    }

    final public function updatedSearchTerm(string $value): void
    {
        $this->search($value);
    }

    final public function search(string $term): void
    {

        $this->reset('options');

        if (mb_strlen($term) < 3) {
            $this->noResultsMessage = '-- Inserisci almeno 3 caratteri --';

            return;
        }

        $this->reset('noResultsMessage');
        $this->options = $this->searchBy($term);
    }

    final public function render()
    {
        return view('livewire.autocomplete');
    }

    /**
     * Name of the event dispatched when an option is selected.
     * Override it to keep the listeners of one autocomplete from reacting to the others in the same page.
     */
    protected function selectedEvent(): string
    {
        return 'option-selected';
    }
}
