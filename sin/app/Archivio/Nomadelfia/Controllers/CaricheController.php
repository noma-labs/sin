<?php

namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use App\Nomadelfia\Models\Cariche;
use Illuminate\Http\Request;


class CaricheController extends CoreBaseController
{
    /**
     * view della pagina index per la gestion delle cariche costituzionali
     * @author Davide Neri
     **/
    public function index()
    {
        $ass = Cariche::AssociazioneCariche();
        $sol = Cariche::SolidarietaCariche();
        $fon = Cariche::FondazioneCariche();
        $agr = Cariche::AgricolaCariche();
        $cul = Cariche::CulturaleCariche();
//    dd($ass);
        return view('nomadelfia.cariche.index', compact('ass', 'sol', 'fon', 'agr', 'cul'));
    }


}
