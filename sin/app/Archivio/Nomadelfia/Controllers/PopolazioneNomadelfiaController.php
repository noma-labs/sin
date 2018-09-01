<?php
namespace App\Nomadelfia\Controllers;
use SnappyPdf;
use Carbon;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Http\Request;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Famiglia;
use App\Anagrafe\Models\Provincia;
use App\Anagrafe\Models\DatiPersonali;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Incarico;

use Validator;

class PopolazioneNomadelfiaController extends CoreBaseController
{
  public function print(){
    $persone = Persona::all();
    $totale = Persona::Presente()->count();
    $maggiorenniUomini = Persona::presente()->uomini()->maggiorenni()->orderBy("nominativo");
    $maggiorenniDonne = Persona::presente()->donne()->maggiorenni()->orderBy("nominativo");
    $minorenni = $this->getMinorenni();
    $minorenniCount = Persona::presente()->minorenni()->count();
    $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->orderBy("nome");
    $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");

    // $pdf = SnappyPdf::loadView("nomadelfia.prova", compact('totale',
    //                                                   "maggiorenniUomini",
    //                                                   "maggiorenniDonne",
    //                                                   "minorenni",
    //                                                   "minorenniCount",
    //                                                   "gruppifamiliari",
    //                                                   "aziende"));
    // $data = Carbon::now();
    // return $pdf->download("popolazione-$data.pdf"); //stream

    $pdf = SnappyPdf::loadView("nomadelfia.elenchi.popolazionenomadelfia", compact("maggiorenniUomini","maggiorenniDonne","minorenni",'minorenniCount'));
      $data = Carbon::now();
      return $pdf->download("popolazione-$data.pdf"); //stream
  }

  public function preview(){
    $persone = Persona::all();
    $totale = Persona::Presente()->count();
    $maggiorenniUomini = Persona::presente()->uomini()->maggiorenni()->orderBy("nominativo");
    $maggiorenniDonne = Persona::presente()->donne()->maggiorenni()->orderBy("nominativo");
    $minorenni = $this->getMinorenni();
    $minorenniCount = Persona::presente()->minorenni()->count();
    $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->orderBy("nome");
    $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");

    // return view("nomadelfia.print",compact('totale',
    //                                         "maggiorenniUomini",
    //                                         "maggiorenniDonne",
    //                                         "minorenni",
    //                                         "minorenniCount",
    //                                         "gruppifamiliari",
    //                                         "aziende"));
    return view("nomadelfia.elenchi.popolazionenomadelfia", compact('totale',
                                            "maggiorenniUomini",
                                            "maggiorenniDonne",
                                            "minorenni",
                                            "minorenniCount",
                                            "gruppifamiliari",
                                            "aziende"));
  }


  public function getMinorenni(){
    $minorenni = Persona::presente()->minorenni()->get();
    $minorenni->map(function($item,$key){
      return $item['anno'] = Carbon::parse($item['data_nascita_persona'])->year;
    });
    $groupMinorenni= $minorenni->sortBy(function ($persona, $key) {
        return $persona['anno'];
    });
    return $groupMinorenni->groupby(['anno', 
                      function($item){
                        return $item['sesso'];
                      }]);
    // return $minorenni;
  }

}
