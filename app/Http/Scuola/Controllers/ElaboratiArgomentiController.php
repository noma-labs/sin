<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\ArgomentoElaborato;
use App\Scuola\Models\Elaborato;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ElaboratiArgomentiController
{
    public function create(int $id): View
    {
        $elaborato = Elaborato::with(Elaborato::argomentiOrdinati())->findOrFail($id);

        return view('scuola.elaborati.argomenti.create', [
            'elaborato' => $elaborato,
            'argomentiUsati' => ArgomentoElaborato::query()->distinct()->orderBy('argomento')->pluck('argomento'),
            'materieUsate' => ArgomentoElaborato::query()->whereNotNull('materia')->distinct()->orderBy('materia')->pluck('materia'),
        ]);
    }

    public function store(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'argomento' => ['required', 'string', 'max:128'],
            'materia' => ['nullable', 'string', 'max:64'],
        ], [
            'argomento.required' => "L'argomento è obbligatorio.",
            'argomento.max' => "L'argomento può avere al massimo 128 caratteri.",
            'materia.max' => 'La materia può avere al massimo 64 caratteri.',
        ]);

        $elaborato = Elaborato::query()->findOrFail($id);
        $elaborato->argomenti()->firstOrCreate([
            'argomento' => $request->string('argomento')->trim()->toString(),
            'materia' => $request->filled('materia') ? $request->string('materia')->trim()->toString() : null,
        ]);

        return to_route('scuola.elaborati.argomenti.create', $elaborato->id)->with('success', 'Argomento aggiunto.');
    }

    public function destroy(int $id, int $argomentoId): RedirectResponse
    {
        $elaborato = Elaborato::query()->findOrFail($id);
        $elaborato->argomenti()->findOrFail($argomentoId)->delete();

        return to_route('scuola.elaborati.argomenti.create', $elaborato->id)->with('success', 'Argomento rimosso.');
    }
}
