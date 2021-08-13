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
        $a = Anno::create([
            'id' => "2034",
            'scolastico' => "2017/2018"
        ]);
        // aggiuni due classi nell'anno e controlla
        $c1 =$a->aggiungiClasse(ClasseTipo::all()->random());
        $p1 =Persona::all()->random();
        $c1->aggiungiAlunno($p1, Carbon::now());

//        $c2 = Classe::aggiungiClasse("2022/2023", $t->get(3));
//        $p2 =Persona::all()->random();
//        $c2->aggiungiAlunno($p2, Carbon::now());

        $classi =  Classe::perAnno("2022/2023");
        return view('scuola.classi.index', compact('classi'));
    }


}
