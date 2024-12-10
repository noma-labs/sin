<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
use Illuminate\Http\Request;

final class AnnoScolasticoClassiController
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

    public function aggiungiClasse(Request $request, $id)
    {
        $request->validate([
            'tipo' => 'required',
        ], [
            'tipo.required' => 'Il tipo di classe da aggiungere è obbligatorio.',
        ]);
        $anno = Anno::FindOrFail($id);
        $classe = $anno->aggiungiClasse(ClasseTipo::findOrFail($request->tipo));

        return redirect()->back()->withSuccess("Classe  {$classe->tipo->nome} aggiunta a {{$anno->scolastico}} con successo.");
    }

    public function clone(Request $request, $id)
    {
        $request->validate([
            'anno_inizio' => 'required',
        ], [
            'anno_inizio.date' => 'La data di inizio non è una data valida.',
            'anno_inizio.required' => 'La data di inizio anno è obbligatoria',
        ]);
        $anno = Anno::FindOrFail($id);
        $aNew = Anno::cloneAnnoScolastico($anno, $request->get('anno_inizio'));

        return redirect()->route('scuola.anno.show', ['id' => $aNew->id])->withSuccess("Anno scolastico $aNew->scolastico aggiunto con successo.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_inizio' => 'required',
        ], [
            'data_inizio.required' => 'La data di inizio anno è obbligatoria',
        ]);
        $year = Carbon::parse($request->get('data_inizio'))->year;
        $anno = Anno::createAnno($year, $request->get('data_inizio'), true);

        return redirect()->back()->withSuccess("Anno scolastico $anno->scolastico aggiunto con successo.");
    }
}
