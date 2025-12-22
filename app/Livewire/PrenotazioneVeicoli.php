<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Livewire\Component;

final class PrenotazioneVeicoli extends Component
{
    public string $dataPartenza;

    public string $dataArrivo;

    public string $oraPartenza;

    public string $oraArrivo;

    public $veicoli = [];

    public int $selectedVeicolo;

    public string $message = '--seleziona veicolo--';

    public function mount($dataPartenza = null, $oraPartenza = null, $dataArrivo = null, $oraArrivo = null, $selectedVeicolo = null): void
    {
        $this->dataPartenza = \Illuminate\Support\Facades\Date::now()->toDateString();
        $this->dataArrivo = \Illuminate\Support\Facades\Date::now()->toDateString();

        if ($dataPartenza) {
            $this->dataPartenza = $dataPartenza;
        }
        if ($oraPartenza) {
            $this->oraPartenza = $oraPartenza;
        }
        if ($dataArrivo) {
            $this->dataArrivo = $dataArrivo;
        }
        if ($oraArrivo) {
            $this->oraArrivo = $oraArrivo;
        }
        if ($selectedVeicolo) {
            $this->selectedVeicolo = $selectedVeicolo;
        }

        if (old('data_par')) {
            $this->dataPartenza = old('data_par');
        }
        if (old('data_arr')) {
            $this->dataArrivo = old('data_arr');
        }
        if (old('ora_par')) {
            $this->oraPartenza = old('ora_par');
        }
        if (old('ora_arr')) {
            $this->oraArrivo = old('ora_arr');
        }

        $this->refreshVeicoli();
    }

    public function updatedDataPartenza(): void
    {
        $this->refreshVeicoli();
    }

    public function updatedOraPartenza(): void
    {
        $this->refreshVeicoli();
    }

    public function updatedDataArrivo(): void
    {
        $this->refreshVeicoli();
    }

    public function updatedOraArrivo(): void
    {
        $this->refreshVeicoli();
    }

    public function refreshVeicoli(): void
    {
        if (! empty($this->dataArrivo) && ! empty($this->dataPartenza) && ! empty($this->oraArrivo) && ! empty($this->oraPartenza)) {

            $this->veicoli = Veicolo::withBookingsIn(\Illuminate\Support\Facades\Date::parse($this->dataPartenza.' '.$this->oraPartenza), \Illuminate\Support\Facades\Date::parse($this->dataArrivo.' '.$this->oraArrivo))
                ->get()->groupBy(['impiego_nome', 'tipologia_nome']);

            $this->reset('message');
        } else {
            $this->message = '--orari di partenza e arrivo non validi--';
        }

    }
}
