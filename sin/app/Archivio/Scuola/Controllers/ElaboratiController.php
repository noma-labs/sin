<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\Models\AnnoScolastico;
use App\Scuola\Models\Anno;

class ElaboratiController extends CoreBaseController
{

    public function index()
    {
        $anno = Anno::getLastAnno();
        $cicloAlunni = $anno->totAlunniPerCiclo();
        $alunni = $anno->alunni();
        $resp = $anno->responsabile;
        return view('scuola.elaborati.insert', compact('anno', 'cicloAlunni', 'alunni', 'resp'));
    }

    public function insert()
    {
        return view('scuola.elaborati.insert');
    }
}
