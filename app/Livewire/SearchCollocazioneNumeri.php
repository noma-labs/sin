<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Biblioteca\Models\ViewCollocazione;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

final class SearchCollocazioneNumeri extends Component
{
    public string $letters;

    public string $numero = '0';

    public string $collocazione = '';

    public $busy = [];

    public $free = [];

    public $next;

    public bool $showBusy;

    public bool $showFree;

    public bool $showNext;

    public string $name;

    /**
     * When true, selecting only the lettere (without a numero) sets the collocazione to the lettere
     * (e.g. "AAA"), so it can be used to search all the books with those lettere.
     */
    public bool $allowLettereOnly;

    final public function mount(bool $showBusy = true, bool $showFree = true, bool $showNext = true, string $name = 'xCollocazione', bool $allowLettereOnly = false): void
    {
        $this->showBusy = $showBusy;
        $this->showFree = $showFree;
        $this->showNext = $showNext;
        $this->name = $name;
        $this->allowLettereOnly = $allowLettereOnly;
    }

    #[On('option-selected')]
    public function selectedLettere(string $option): void
    {
        $this->letters = $option;
        if ($option === 'SENZA_COLLOCAZIONE') {
            $this->collocazione = 'null';

            return;
        }

        if ($option === 'null') {
            $this->collocazione = 'null';

            return;
        }

        $this->reset('numero', 'busy', 'free', 'next');
        $this->collocazione = $this->collocazioneWithoutNumero();

        $num = ViewCollocazione::numeri($this->letters)->get();
        if ($num->isEmpty()) {
            return;
        }
        $this->busy = $num->pluck('numeri')->toArray();
        $max = ViewCollocazione::query()->MaxForLettere($this->letters);
        $arr2 = range(1, $max);
        $this->free = array_diff($arr2, $this->busy);
        $this->next = $max + 1;
    }

    public function searchBy(string $term): array
    {
        $collocazioni = ViewCollocazione::lettere()
            ->where('lettere', 'LIKE', $term.'%')
            ->get();

        $options = [];
        foreach ($collocazioni as $colloc) {
            $options[] = new Option($colloc->lettere, $colloc->lettere);
        }

        return $options;
    }

    public function selected(array|int|string $ids): array
    {
        $collocazioni = ViewCollocazione::query()
            ->whereIn('COLLOCAZIONE', $ids)
            ->orderBy('COLLOCAZIONE')
            ->get();

        $selected = [];
        foreach ($collocazioni as $colloc) {
            $options[] = new Option($colloc->lettere, $colloc->lettere);
        }

        return $selected;
    }

    final public function updatedNumero(string $number): void
    {
        if (in_array(mb_trim($number), ['', '0'], true)) {
            $this->collocazione = $this->collocazioneWithoutNumero();

            return;
        }

        $this->collocazione = $this->letters.Str::padLeft(mb_trim($number), 3, '0');
    }

    public function render(): string
    {
        return <<<'HTML'
        <div class="">
            <label class="form-label">Numeri(*)</label>
            <input type="hidden" name="{{$name}}" value="{{ $collocazione }}">
            <select class="form-control" wire:model.live="numero">
                <!-- HACK: use value="0" to force the select to show this option instaed of the numero =     -->
                <option value="0" selected>
                    Sel. numeri
                </option>
                @if($showBusy)
                <optgroup label="Numeri assegnati" >
                    @foreach ($busy as $numero)
                        <option>{{$numero}}</option>
                    @endforeach
                </optgroup>
                @endif
                @if($showFree)
                <optgroup label="Numeri liberi">
                    @foreach ($free as $free)
                        <option>{{$free}}</option>
                    @endforeach
                </optgroup>
                @endif
                @if($showNext)
                <optgroup label="Nuovo Numero">
                        @if($next)
                        <option> {{$next}}</option>
                    @endif
                </optgroup>
                @endif
            </select>
        </div>
        HTML;
    }

    private function collocazioneWithoutNumero(): string
    {
        return $this->allowLettereOnly ? $this->letters : '';
    }
}
