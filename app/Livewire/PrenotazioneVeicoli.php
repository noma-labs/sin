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
        $this->dataPartenza = Carbon::now()->toDateString();
        $this->dataArrivo = Carbon::now()->toDateString();

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
        if (!empty($this->dataArrivo) && !empty($this->dataPartenza) && !empty($this->oraArrivo) && !empty($this->oraPartenza)) {
            $ordinamento = [
                ['tipologia' => '1', 'impiego' => '1'], // autovetture grosseto
                ['tipologia' => '6', 'impiego' => '1'], // furgoncini grosseto
                ['tipologia' => '7', 'impiego' => '1'], // furgoni grosseto
                ['tipologia' => '1', 'impiego' => '3'], // autovetture viaggi lunghi
                ['tipologia' => '6', 'impiego' => '3'], // furgoncini viaggi lunghi
                ['tipologia' => '7', 'impiego' => '3'], // furgoni viaggi lunghi
                ['tipologia' => '10', 'impiego' => '1'], // motocicli grosseto
                ['tipologia' => '5', 'impiego' => '1'], // ciclomotori grosseto
                ['tipologia' => '10', 'impiego' => '3'], // motocicli viaggi lunghi
                ['tipologia' => '5', 'impiego' => '3'], // ciclomotori viaggi lunghi
                ['tipologia' => '3', 'impiego' => '3'], // autocarri viaggi lunghi
                ['tipologia' => '2', 'impiego' => '3'], // autobus viaggi lunghi
                ['tipologia' => '1', 'impiego' => '4'], // autovetture personale
                ['tipologia' => '6', 'impiego' => '4'], // furgoncini personale
                ['tipologia' => '7', 'impiego' => '4'], // furgoni personale
                ['tipologia' => '1', 'impiego' => '5'], // autovetture roma
                ['tipologia' => '6', 'impiego' => '5'], // furgoncini roma
                ['tipologia' => '7', 'impiego' => '5'], // furgoni roma
                ['tipologia' => '3', 'impiego' => '5'], // autocarri roma
            ];

            $veicoli = Veicolo::withBookingsIn(Carbon::parse($this->dataPartenza . ' ' . $this->oraPartenza), Carbon::parse($this->dataArrivo . ' ' . $this->oraArrivo))
                ->get();

            $veicoliOrdinati = collect([]);

            foreach ($ordinamento as $ord) {
                $veicoliDaAggiungere = $veicoli->where('tipologia_id', $ord['tipologia'])
                    ->where('impiego_id', $ord['impiego']);

                // Aggiungi i veicoli trovati alla collection ordinata
                $veicoliOrdinati = $veicoliOrdinati->merge($veicoliDaAggiungere);

                // Rimuovi i veicoli aggiunti dalla collection originale
                $veicoli = $veicoli->reject(function ($veicolo) use ($veicoliDaAggiungere) {
                    return $veicoliDaAggiungere->contains('id', $veicolo->id);
                });
            }

            // Aggiungi eventuali veicoli rimanenti che non sono stati ordinati
            $veicoliOrdinati = $veicoliOrdinati->merge($veicoli);

            $this->veicoli = $veicoliOrdinati->groupBy(['impiego_nome', 'tipologia_nome']);
            $this->reset('message');
        } else {
            $this->message = '--orari di partenza e arrivo non validi--';
        }

    }
}
