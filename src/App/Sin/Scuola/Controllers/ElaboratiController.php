<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use App\Scuola\Requests\AddElaboratoRequest;

class ElaboratiController extends CoreBaseController
{
    public function index()
    {
        $anno = Anno::getLastAnno();
        $cicloAlunni = Studente::InAnnoScolasticoPerCiclo($anno)->get();
        $alunni = $anno->alunni();
        $resp = $anno->responsabile;

        return view('scuola.elaborati.insert', compact('anno', 'cicloAlunni', 'alunni', 'resp'));
    }

    public function insert()
    {
        return view('scuola.elaborati.insert');
    }

    public function insertConfirm(AddElaboratoRequest $request)
    {
        return view('scuola.elaborati.insert');
    }
}
