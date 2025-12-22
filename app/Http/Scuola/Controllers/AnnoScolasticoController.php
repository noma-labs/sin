<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolasticoData;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
use Illuminate\Http\Request;

final class AnnoScolasticoController
{
    public function show(Request $request, $id)
    {
        $anno = Anno::find($id);

        $alunni = Studente::InAnnoScolastico($anno)->count();
        $cicloAlunni = Studente::InAnnoScolasticoPerCiclo($anno)->get();
        $resp = $anno->responsabile;
        $classi = $anno->classi()->with('tipo')->get()->sortBy('tipo.ord');

        return view('scuola.anno.show', compact('anno', 'cicloAlunni', 'alunni', 'resp', 'classi'));
    }

    public function showNew(Request $request, $id)
    {
        $anno = Anno::findOrFail($id);

        $anno = AnnoScolasticoData::FromDatabase($anno);

        return view('scuola.anno.show-new', compact('anno'));
    }

    public function clone(Request $request, $id)
    {
        $request->validate([
            'anno_inizio' => ['required'],
        ], [
            'anno_inizio.date' => 'La data di inizio non è una data valida.',
            'anno_inizio.required' => 'La data di inizio anno è obbligatoria',
        ]);
        $anno = Anno::FindOrFail($id);
        $aNew = Anno::cloneAnnoScolastico($anno, $request->get('anno_inizio'));

        return to_route('scuola.anno.show', ['id' => $aNew->id])->withSuccess("Anno scolastico $aNew->scolastico aggiunto con successo.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_inizio' => ['required'],
        ], [
            'data_inizio.required' => 'La data di inizio anno è obbligatoria',
        ]);
        $year = \Illuminate\Support\Facades\Date::parse($request->get('data_inizio'))->year;
        $anno = Anno::createAnno($year, $request->get('data_inizio'), true);

        return back()->withSuccess("Anno scolastico $anno->scolastico aggiunto con successo.");
    }
}
