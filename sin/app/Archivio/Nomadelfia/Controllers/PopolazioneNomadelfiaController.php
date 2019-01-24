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
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Incarico;

use Validator;

class PopolazioneNomadelfiaController extends CoreBaseController
{

  public function printPersone(){


  }
  
  public function print(Request $request){
    $elenchi = collect($request->elenchi);
    if($elenchi->contains("personeeta")){
      $maggiorenni= Persona::presente()->maggiorenni()->orderBy("nominativo");
      $maggiorenniDonne = Persona::presente()->donne()->maggiorenni()->orderBy("nominativo");
      $minorenni = $this->getMinorenni();
      $minorenniCount = Persona::presente()->minorenni()->count();
    }
    
    if($elenchi->contains("personestati"))
      $personestati = true;
    if($elenchi->contains("personeposizioni"))
      $personeposizioni = true;
    if($elenchi->contains("famiglie"))
      $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->orderBy("nome");
    if($elenchi->contains("gruppi"))
      $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->orderBy("nome");
    if($elenchi->contains("aziende"))
      $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");
    if($elenchi->contains("scuola"))
      $scuola = [];


    $pdf = SnappyPdf::loadView("nomadelfia.elenchi.popolazionenomadelfia", compact('totale',
                                                      "maggiorenni",
                                                      "maggiorenniDonne",
                                                      "minorenni",
                                                      "minorenniCount",
                                                      "personestati",
                                                      "personeposizioni",
                                                      "gruppifamiliari",
                                                      "aziende"));
    // viewport-size must be set otherwise the pdf will be bad formatted
    $pdf->setOption('viewport-size','1280x1024');
    $data = Carbon::now();
    return $pdf->download("popolazione-$data.pdf"); //stream
  }

  public function preview(){
    $maggiorenni= Persona::presente()->maggiorenni()->orderBy("nominativo");
    $maggiorenniDonne = Persona::presente()->donne()->maggiorenni()->orderBy("nominativo");
    $minorenni = $this->getMinorenni();
    $minorenniCount = Persona::presente()->minorenni()->count();
    $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->orderBy("nome");
    $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");

    return view("nomadelfia.elenchi.popolazionenomadelfia", compact(
                                            "maggiorenni",
                                            "maggiorenniDonne",
                                            "minorenni",
                                            "minorenniCount",
                                            "gruppifamiliari",
                                            "aziende"));
  }


  public function getMinorenni(){
    $minorenni = Persona::presente()->minorenni()->get();
    $minorenni->map(function($item,$key){
      return $item['anno'] = Carbon::parse($item['data_nascita'])->year;
    });
    $groupMinorenni= $minorenni->sortBy(function ($persona, $key) {
        return $persona['anno'];
    });
    return $groupMinorenni->groupby(['anno', 
                      function($item){
                        return $item['sesso'];
                      }]);
  }

}
