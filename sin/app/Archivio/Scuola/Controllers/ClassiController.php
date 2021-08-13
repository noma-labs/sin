<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use App\Nomadelfia\Models\Classe;
use App\Nomadelfia\Models\ClasseTipo;
use App\Nomadelfia\Models\Persona;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Nomadelfia\Models\AnnoScolastico;

class ClassiController extends CoreBaseController
{
    public function index()
    {
        $t = ClasseTipo::all();
        // aggiuni due classi nell'anno e controlla
        $c1 = Classe::aggiungiClasse("2021/2022", $t->get(0));
        $p1 =Persona::all()->random();
        $c1->aggiungiAlunno($p1, Carbon::now());

        $c2 = Classe::aggiungiClasse("2021/2022", $t->get(3));
        $p2 =Persona::all()->random();
        $c2->aggiungiAlunno($p2, Carbon::now());

        $classi =  Classe::perAnno("2021/2022");
        return view('scuola.classi.index', compact('classi'));
    }


}
