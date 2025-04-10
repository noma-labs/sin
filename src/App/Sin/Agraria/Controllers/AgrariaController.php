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
        // TODO: show the next maintenance for each vehicle (not a list of all the next maintenances)
        $ultime = $this->getManutenzioniFatte();
        $prossime = $this->getProssimeManutenzioni($mezzi);

        if ($this->controllaOre($mezzi)) {
            $errors = collect(['Le ore lavorative dei trattori non sono state aggiornate da più di un mese. <a  class="btn btn-sm btn-danger" href="'.route('agraria.vehicle.hour.create').'">Aggiorna ore</a>']);

            return view('agraria.home', compact('mezzi', 'ultime', 'prossime'))->with('errors', $errors);
        }

        return view('agraria.home', compact('mezzi', 'ultime', 'prossime'));
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

    public function getManutenzioniFatte(): array
    {
        $manutenzioni = Manutenzione::orderBy('data', 'desc')->take(5)->get();
        $res = [];
        foreach ($manutenzioni as $m) {
            $mezzo = MezzoAgricolo::find($m->mezzo_agricolo);
            $prog = $m->programmate()->get();
            $lavori = [];
            if ($m->lavori_extra !== null) {
                $lavori[] = mb_strtolower($m->lavori_extra);
            }
            if ($prog->isNotEmpty()) {
                foreach ($prog as $p) {
                    $lavori[] = mb_strtolower((string) $p->nome);
                }
            }
            $new = [
                'data' => $m->data,
                'persona' => $m->persona,
                'mezzo' => $mezzo->nome,
                'lavori' => implode(', ', $lavori),
            ];
            $res[] = $new;
        }

        return $res;
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
