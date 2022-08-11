<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use App\Scuola\Models\AddStudentAction;
use App\Scuola\Models\Classe;
use App\Scuola\Models\ClasseTipo;
use App\Nomadelfia\Models\Persona;
use App\Scuola\Models\Anno;
use App\Scuola\Requests\AddCoordinatoreRequest;
use App\Scuola\Requests\AddStudentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Nomadelfia\Models\AnnoScolastico;

class ClassiController extends CoreBaseController
{
    public function index(int $anno_id)
    {
        $anno = Anno::firstOrFail($anno_id);
        $classi = $anno->classi()->get();
        return view('scuola.classi.index', compact('anno', 'classi'));
    }

    public function show($id)
    {
        $classe = Classe::findOrFail($id);
        $alunni = $classe->alunni();
        $coords = $classe->coordinatori();
        $possibili = $classe->alunniPossibili();
        $coordPossibili = $classe->coordinatoriPossibili();
        return view('scuola.classi.show', compact('classe', 'alunni', 'coords', 'possibili', 'coordPossibili'));
    }

    public function aggiungiAlunno(AddStudentRequest $request, $id, AddStudentAction $addStudentAction)
    {
        $request->validated();
        $classe = Classe::findOrFail($id);
        $alunno = Persona::findOrFail($request->alunno_id);
        $addStudentAction->execute($classe, $alunno, $request->data_inizio);
        return redirect()->back()->withSuccess("Alunno $alunno->nominativo aggiunto a {$classe->tipo->nome} con successo.");
    }

    public function rimuoviAlunno(Request $request, $id, $alunno_id)
    {
        $classe = Classe::findOrFail($id);
        $alunno = Persona::findOrFail($alunno_id);
        $classe->rimuoviAlunno($alunno);
        return redirect()->back()->withSuccess("Alunno $alunno->nominativo  eliminato da {$classe->tipo->nome} con successo.");
    }

    public function aggiungiCoordinatore(AddCoordinatoreRequest $request, $id)
    {
        $request->validated();
        $classe = Classe::findOrFail($id);
        $coord = Persona::findOrFail($request->coord_id);

        $classe->aggiungiCoordinatore($coord, $request->data_inizio, $request->coord_tipo);
        return redirect()->back()->withSuccess("Coordiantore $coord->nominativo  aggiunto a {$classe->tipo->nome} con successo.");
    }

    public function rimuoviCoordinatore(Request $request, $id, $coord_id)
    {
        $classe = Classe::findOrFail($id);
        $coord = Persona::findOrFail($coord_id);
        $classe->rimuoviCoordinatore($coord);
        return redirect()->back()->withSuccess("Coordinatore $coord->nominativo  eliminato da {$classe->tipo->nome} con successo.");
    }


}
