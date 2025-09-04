<?php

declare(strict_types=1);

namespace App\Patente\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Patente\Models\CategoriaPatente;
use App\Patente\Models\CQC;
use App\Patente\Models\Patente;
use Illuminate\Http\Request;

final class PatenteSearchController
{
    public function searchView()
    {
        $categorie = CategoriaPatente::orderby('categoria')->get();
        $cqc = CQC::orderby('categoria')->get();

        return view('patente.search', compact('categorie', 'cqc'));
    }

    public function search(Request $request)
    {
        if (! $request->except(['_token'])) {
            return redirect()->back()->withError('Nessun criterio di ricerca inserito.');
        }

        $msgSearch = ' ';
        $orderBy = 'numero_patente';
        $queryPatenti = Patente::where(function ($q) use ($request, &$msgSearch, &$orderBy): void {
            if ($request->filled('persona_id')) {
                $persona = $request->persona_id;
                $q->where('persone_patenti.persona_id', $persona);
                $nome = Persona::findorfail($persona)->nominativo;
                $msgSearch = $msgSearch.'Persona='.$nome;

            }
            if ($request->filled('numero_patente')) {
                $numero_patente = $request->numero_patente;
                $q->where('numero_patente', 'LIKE', "$numero_patente%");
                $msgSearch = $msgSearch.' numero_patente='.$numero_patente;
            }
            if ($request->filled('criterio_data_rilascio') and $request->filled('data_rilascio')) {
                $q->where('data_rilascio_patente', $request->input('criterio_data_rilascio'), $request->input('data_rilascio'));
                $msgSearch = $msgSearch.' Data Rilascio'.$request->input('criterio_data_rilascio').$request->input('data_rilascio');
            }
            if ($request->filled('criterio_data_scadenza') and $request->filled('data_scadenza')) {
                $q->where('data_scadenza_patente', $request->input('criterio_data_scadenza'), $request->input('data_scadenza'));
                $orderBy = 'data_scadenza_patente';
                $msgSearch = $msgSearch.' Data scadenza'.$request->input('criterio_data_scadenza').$request->input('data_scadenza');
            }
            if ($request->filled('cqc_patente')) {
                $cqc = $request->cqc_patente;
                $q->whereHas('cqc', function ($q) use ($cqc, &$msgSearch, $request): void {
                    $q->where('id', $cqc);
                    if ($request->filled('criterio_cqc_data_scadenza') and $request->filled('cqc_data_scadenza')) {
                        $q->where('data_scadenza', $request->input('criterio_cqc_data_scadenza'), $request->input('cqc_data_scadenza'));
                        $msgSearch = $msgSearch.' data scadenza '.$request->input('criterio_cqc_data_scadenza').$request->input('cqc_data_scadenza');
                    }
                });

                $nome = CQC::findorfail($cqc)->categoria;
                $msgSearch = $msgSearch.' cqc='.$nome;
            }
            if ($request->filled('categoria_patente')) {
                $categoria = $request->categoria_patente;
                $q->whereHas('categorie', function ($q) use ($categoria): void {
                    $q->where('id', $categoria);
                });
                $nome = CategoriaPatente::findorfail($categoria)->categoria;
                $msgSearch = $msgSearch.' categoria='.$nome;
            }
        });
        // $msgSearch=$msgSearch."order by: $orderBy";
        $patenti = $queryPatenti->sortable($orderBy, 'asc')->paginate(25);

        $categorie = CategoriaPatente::orderby('categoria')->get();
        $cqc = CQC::orderby('categoria')->get();

        return view('patente.search', compact('patenti', 'categorie', 'cqc', 'msgSearch'));
    }
}
