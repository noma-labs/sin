<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use App\Scuola\Models\Classe;
use App\Scuola\Models\ClasseTipo;
use App\Nomadelfia\Models\Persona;
use App\Scuola\Models\Anno;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Nomadelfia\Models\AnnoScolastico;

class ClassiController extends CoreBaseController
{
    public function index()
    {
        $anno = Anno::getLastAnno();
        $classi = $anno->classi()->get();
        return view('scuola.classi.index', compact('anno', 'classi'));
    }

    public function show(Request $request, $id)
    {
        $classe = Classe::findOrFail($id);
        $alunni = $classe->alunni();
        $possibili = $classe->alunniPossibili();
        return view('scuola.classi.show', compact('classe', 'alunni', 'possibili'));
    }

    public function aggiungeAlunno(Request $request, $id)
    {
        $validatedData = $request->validate([
            "alunno_id" => "required",
        ], [
            "alunno_id.required" => "Alunno è obbligatorio",
        ]);
        $classe = Classe::findOrFail($id);
        $alunno = Persona::findOrFail($request->alunno_id);

        $classe->aggiungiAlunno($alunno, $request->data_inizio);
        return redirect()->back()->withSuccess("Alunno $alunno->nominativo  aggiunto a {$classe->tipo->nome} con successo.");
    }

    public function rimuoviAlunno(Request $request, $id, $alunno_id)
    {
        $classe = Classe::findOrFail($id);
        $alunno = Persona::findOrFail($alunno_id);
        $classe->rimuoviAlunno($alunno);
        return redirect()->back()->withSuccess("Alunno $alunno->nominativo  eliminato da {$classe->tipo->nome} con successo.");
    }

}
