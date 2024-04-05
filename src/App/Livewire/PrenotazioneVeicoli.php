<?php

namespace App\Livewire;

use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Veicolo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PrenotazioneVeicoli extends Component
{
    public $veicoli = [];

    public $dataPartenza;
    public $dataArrivo;
    public $oraPartenza;
    public $oraArrivo;

    public function mount()
    {

        $this->veicoli = DB::connection('db_officina')
            ->table('veicolo')
            ->selectRaw('veicolo.id, veicolo.nome, db_nomadelfia.persone.nominativo, impiego.nome as impiego_nome , tipologia.nome as tipologia_nome, prenotazioni.id as prenotazione_id, concat(prenotazioni.data_partenza, ":",  prenotazioni.ora_partenza) as partenza, concat(prenotazioni.data_arrivo, ":", prenotazioni.ora_arrivo) as arrivo')
            ->leftJoin('prenotazioni', 'prenotazioni.veicolo_id', '=', 'veicolo.id')
            ->leftJoin('db_nomadelfia.persone', 'prenotazioni.cliente_id', '=', 'persone.id')
            ->leftJoin('impiego', 'impiego.id', '=', 'veicolo.impiego_id')
            ->leftJoin('tipologia', 'tipologia.id', '=', 'veicolo.tipologia_id')
            ->where('veicolo.prenotabile', 1)
            ->get()
            ->groupBy(['impiego_nome','tipologia_nome']);
    }

    public function render()
    {
        return view('livewire.prenotazione-veicoli');
    }

    public function search()
    {
        Prenotazioni::today()->with(['cliente', 'veicolo.tipologia'])->get();

        Veicolo::prenotabili()->with(['impiego','tipologia'])->get()->groupBy(['impiego.nome','tipologia.nome']);

       dd($this->dataPartenza);
    }
}
