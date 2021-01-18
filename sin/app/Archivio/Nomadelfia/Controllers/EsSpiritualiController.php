<?php
namespace App\Nomadelfia\Controllers;

use Illuminate\Http\Request;
use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\Models\GruppoFamiliare;
use Illuminate\Support\Facades\DB;
use App;

use App\Nomadelfia\Models\EserciziSpirituali;
use App\Nomadelfia\Models\Persona;

class EsSpiritualiController extends CoreBaseController
{
    public function index(Request $request)
    {
        $esercizi = EserciziSpirituali::attivi()->get();
        return view("nomadelfia.esercizi.index", compact('esercizi'));
    }
    
    public function show(Request $request, $id)
    {
        $esercizio = EserciziSpirituali::findOrFail($id);
        $persone = $esercizio->personeOk();
        return view("nomadelfia.esercizi.show", compact('esercizio', 'persone'));
    }

    public function assegnaPersona(Request $request, $id)
    {
        $validatedData = $request->validate([
            "persona_id" => "required",
        ], [
            "persona_id.required" => "Persona è obbligatoria",
        ]);
        $persona = Persona::findOrFail($request->persona_id);
        $esercizi = EserciziSpirituali::findOrFail($id);
        $esercizi->aggiungiPersona($persona);
        return redirect()->back()->withSuccess("Persona $persona->nominativo aggiunta con successo.");
    }

    public function eliminaPersona(Request $request, $id, $idPersona)
    {
        $esercizio = EserciziSpirituali::findOrFail($id);
        $persona = Persona::findOrFail($idPersona);
        $esercizio->eliminaPersona($persona);
        return redirect()->back()->withSuccess("Persona $persona->nominativo eliminata con successo.");
    }
}
