<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\AddStudentAction;
use App\Scuola\Models\Classe;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class ClassiController
{
    public function show($id)
    {
        $classe = Classe::findOrFail($id);
        $anno = $classe->anno()->first();
        $alunni = $classe->alunni();
        $coords = $classe->coordinatori();
        $alunniPossibili = $classe->alunniPossibili();
        $coordPossibili = $classe->coordinatoriPossibili();

        return view('scuola.classi.show', compact('anno', 'classe', 'alunni', 'coords', 'alunniPossibili', 'coordPossibili'));
    }

    public function aggiungiAlunno(Request $request, $id, AddStudentAction $addStudentAction)
    {
        $request->validate([
            'alunno_id' => 'required',
            'data_inizio' => 'required|date',
        ], [
            'alunno_id.required' => 'Alunno è obbligatorio.',
            'data_inizio.required' => 'Data inizio è obbligatoria.',
            'data_inizio.date' => 'Data inizio non è valida.',
        ]);
        $classe = Classe::findOrFail($id);
        $alunni = $request->get('alunno_id');
        foreach ($alunni as $id) {
            $alunno = Persona::findOrFail($id);
            $addStudentAction->execute($classe, $alunno, $request->get('data_inizio'));
        }

        return redirect()->back()->withSuccess("Alunno/i aggiunto a {$classe->tipo->nome} con successo.");
    }

    public function delete($id)
    {
        $classe = Classe::findOrFail($id);
        $classe->delete();

        return redirect()->back()->withSuccess('Calsse eliminata con successo.');
    }

    public function rimuoviAlunno(Request $request, $id, $alunno_id)
    {
        $classe = Classe::findOrFail($id);
        $alunno = Persona::findOrFail($alunno_id);
        $classe->rimuoviAlunno($alunno);

        return redirect()->back()->withSuccess("Alunno $alunno->nominativo  eliminato da {$classe->tipo->nome} con successo.");
    }
}
