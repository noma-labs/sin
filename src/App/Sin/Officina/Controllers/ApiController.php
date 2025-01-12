<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\TipoFiltro;
use App\Officina\Models\TipoGomme;
use App\Officina\Models\Veicolo;
use Illuminate\Http\Request;
use Throwable;

final class ApiController
{
    public function eliminaGomma(Request $request): array
    {
        $veicolo = Veicolo::find($request->input('veicolo'));
        try {
            $veicolo->gomme()->detach($request->input('gomma'));
        } catch (Throwable) {
            return ['error'];
        }

        return ['success'];
    }

    public function gomme()
    {
        $gomme = TipoGomme::orderBy('codice')->get();
        $result = [];
        foreach ($gomme as $gomma) {
            $result[] = ['codice' => $gomma->codice.' '.$gomma->note, 'id' => $gomma->id];
        }

        return response()->json($result);
    }

    public function nuovaGomma(Request $request): array
    {
        if ($request->input('note') === '') {
            $note = '';
        } else {
            $note = $request->input('note');
        }
        if ($request->input('gomma_id') === '') {
            // salvo la nuova gomma nel db
            try {
                $gomma = TipoGomme::create([
                    'codice' => mb_strtoupper($request->input('nuovo_codice')),
                    'note' => $note,
                ]);
            } catch (Throwable) {
                return [
                    'status' => 'error',
                    'msg' => "Errore: codice della gomma gia' presente ".$request->input('nuovo_codice').' '.($request->input('note') === ''),
                ];
            }
        } else {
            $gomma = TipoGomme::find($request->input('gomma_id'));
        }
        $veicolo = Veicolo::find($request->input('veicolo_id'));
        try {
            $veicolo->gomme()->attach($gomma->id);
        } catch (Throwable) {
            return ['status' => 'error', 'msg' => "Errore: il veicolo ha gia' questo tipo di gomma"];
        }

        return ['status' => 'ok'];
    }

    public function filtri(): array
    {
        $filtri = TipoFiltro::all()->sortBy('tipo');
        $result = [];
        foreach ($filtri as $value) {
            $result[] = $value;
        }

        return $result;
    }

    public function tipiFiltro(): array
    {
        return TipoFiltro::tipo();
    }

    public function eliminaFiltro(Request $request): array
    {
        $filtro = TipoFiltro::find($request->input('filtro'));
        try {
            $filtro->delete();
        } catch (Throwable) {
            return ['status' => 'error', 'msg' => "Errore nell'eliminazione del filtro"];
        }

        return ['status' => 'success', 'msg' => 'Filtro eliminato'];
    }
}
