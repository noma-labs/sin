<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\Manutenzione;
use App\Agraria\Models\MezzoAgricolo;
use App\Agraria\Models\StoricoOre;
use Carbon\Carbon;

final class AgrariaController
{
    public function index()
    {
        $mezzi = MezzoAgricolo::orderBy('nome')->get();
        $mezziCostosi = MezzoAgricolo::query()
            ->select('mezzo_agricolo.id', 'mezzo_agricolo.nome')
            ->join('manutenzione', 'mezzo_agricolo.id', '=', 'manutenzione.mezzo_agricolo')
            ->selectRaw('SUM(CASE WHEN manutenzione.spesa IS NULL OR manutenzione.spesa = "" THEN 0 ELSE manutenzione.spesa END) as totale_spesa')
            ->groupBy('mezzo_agricolo.id', 'mezzo_agricolo.nome')
            ->orderByDesc('totale_spesa')
            ->take(5)
            ->get();
        $done = Manutenzione::with('mezzo', 'programmate')->orderBy('data', 'desc')->take(3)->get();
        $prossime = $this->getProssimeManutenzioni($mezzi);

        // Calcola il costo totale delle manutenzioni dell'anno corrente (da gennaio)
        $startOfYear = Carbon::now()->startOfYear()->toDateString();
        $today = Carbon::now()->toDateString();
        $costCurrentYear = Manutenzione::whereBetween('data', [$startOfYear, $today])->sum('spesa');

        // Calcola il costo totale delle manutenzioni dell'anno precedente (da gennaio a dicembre)
        $startOfLastYear = Carbon::now()->subYear()->startOfYear()->toDateString();
        $todayLastYear = Carbon::now()->subYear()->toDateString();
        $costLastYear = Manutenzione::whereBetween('data', [$startOfLastYear, $todayLastYear])->sum('spesa');

        // Calcola la variazione percentuale YoY
        $yoyPerc = null;
        if ($costLastYear !== 0) {
            $yoyPerc = (($costCurrentYear - $costLastYear) / $costLastYear) * 100;
        }

        if ($this->controllaOre($mezzi)) {
            $errors = collect(['Le ore lavorative dei trattori non sono state aggiornate da più di un mese. <a  class="btn btn-sm btn-danger" href="'.route('agraria.vehicle.hour.create').'">Aggiorna ore</a>']);

            return view('agraria.home', compact('mezziCostosi', 'done', 'prossime', 'costCurrentYear', 'yoyPerc'))->with('errors', $errors);
        }

        return view('agraria.home', compact('mezziCostosi', 'done', 'prossime', 'costCurrentYear', 'yoyPerc'));
    }

    public function controllaOre($m): bool
    {
        foreach ($m as $mezzo) {
            $ultimo_aggiornamento = StoricoOre::where('mezzo_agricolo', $mezzo->id)->orderBy('data', 'desc')->first();
            if ($ultimo_aggiornamento === null) {
                return true;
            }
            $data = new Carbon($ultimo_aggiornamento->data);
            if ($data->diffInDays(Carbon::now(), true) > 30) {
                return true;
            }
        }

        return false;
    }

    public function getProssimeManutenzioni($mezzi)
    {
        $res = collect([]);
        foreach ($mezzi as $m) {
            $man = $m->scadenzaManutenzioni()->sort()->all();
            foreach ($man as $k => $v) {
                $t = [
                    'id' => $m->id,
                    'nome' => $m->nome,
                    'manutenzione' => $k,
                    'ore' => $v,
                ];
                $res->push($t);
            }
        }

        return $res->sortBy('ore')->take(10);
    }
}
