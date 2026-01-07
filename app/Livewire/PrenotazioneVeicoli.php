<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Officina\Models\Veicolo;
use Livewire\Component;
use App\Officina\Models\ViewClienti;
use App\Officina\Models\ViewMeccanici;
use App\Officina\Models\Uso;
use Illuminate\Validation\Rule;
use App\Officina\Models\Prenotazioni;
use App\Nomadelfia\Persona\Models\Persona;

final class PrenotazioneVeicoli extends Component
{
    public ?string $dataPartenza = null;
    public ?string $dataArrivo = null;
    public ?string $oraPartenza = null;
    public ?string $oraArrivo = null;
    public ?int $selectedVeicolo = null;
    public ?int $selectedCliente = null;
    public ?int $selectedMeccanico = null;
    public ?int $selectedUso = null;
    public ?string $destinazione = null;
    public ?string $note = null;
    public string $veicoloSelectPlaceholder = '--Seleziona--';
    public $veicoli = [];
    public $clienti = [];
    public $meccanici = [];
    public $usi = [];

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

        $this->refreshVeicoli();

        $this->clienti = ViewClienti::orderBy('nominativo', 'asc')->get();
        $this->meccanici = ViewMeccanici::orderBy('nominativo')->get();
        $this->usi = Uso::all();
    }

    protected function rules() 
    {
        return [
            'dataPartenza' => 'required|date',
            'dataArrivo' => 'required|date|after_or_equal:dataPartenza',
            'oraPartenza' => 'required',
            'oraArrivo' => [
                'required',
                Rule::when($this->dataPartenza === $this->dataArrivo, ['after:oraPartenza']),
            ],
            'selectedVeicolo' => 'required',
            'selectedCliente' => 'required',
            'selectedMeccanico' => 'required',
            'selectedUso' => 'required',
            'destinazione' => 'required',
            'note' => 'nullable|string',
        ];
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

            $this->reset('veicoloSelectPlaceholder');
        } else {
            $this->veicoloSelectPlaceholder = '--orari di partenza e arrivo non validi--';
        }

    }

    public function saveReservation()
    {
        $this->validate();
 
        Prenotazioni::create([
            'cliente_id' => $this->selectedCliente,
            'veicolo_id' => $this->selectedVeicolo,
            'meccanico_id' => $this->selectedMeccanico,
            'data_partenza' => $this->dataPartenza,
            'ora_partenza' => $this->oraPartenza,
            'data_arrivo' => $this->dataArrivo,
            'ora_arrivo' => $this->oraArrivo,
            'uso_id' => $this->selectedUso,
            'note' => $this->note,
            'destinazione' => $this->destinazione,
        ]);
 
        session()->flash('success', 'Prenotazione eseguita con successo.');
 
        return redirect()->to(request()->header('Referer'));
    }
}
