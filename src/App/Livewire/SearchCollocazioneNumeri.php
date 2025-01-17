<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Biblioteca\Models\ViewCollocazione;
use Livewire\Attributes\On;
use Livewire\Component;

final class SearchCollocazioneNumeri extends Component
{
    public $busy = [];
    public $free = [];
    public $next = null;

    public bool $showBusy;
    public bool $showFree;
    public bool$showNext;

    final public function mount(bool $showBusy = true, bool $showFree = true, bool $showNext = true): void
    {
        $this->showBusy = $showBusy;
        $this->showFree = $showFree;
        $this->showNext = $showNext;
    }

    #[On('option-selected')]
    public function selectedLettere(int|string $option)
    {
        $this->busy = ViewCollocazione::numeri($option)->get()->pluck('numeri')->toArray();
        $max = ViewCollocazione::MaxForLettere($option);
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
            $options[] = new Option($colloc->lettere , $colloc->lettere);
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
            $options[] = new Option($colloc->lettere , $colloc->lettere);
        }

        return $selected;
    }

    public function render()
    {
        return <<<'HTML'
        <div class="form-group">
            <label class="form-label">Numeri(*)</label>
            <select class="form-control">
            <option disabled selected hidden>
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
}
