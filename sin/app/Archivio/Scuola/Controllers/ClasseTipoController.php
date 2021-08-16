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

class ClasseTipoController extends CoreBaseController
{
    public function index()
    {
        $tipi = ClasseTipo::all();
        return view('scuola.tipi.index', compact('tipi'));
    }

}
