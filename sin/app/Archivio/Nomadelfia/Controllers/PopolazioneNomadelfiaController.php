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
use App\Nomadelfia\Models\PopolazioneNomadelfia;

use Illuminate\Support\Facades\DB;

use Validator;


class PopolazioneNomadelfiaController extends CoreBaseController
{
  // https://laracasts.com/discuss/channels/laravel/is-this-logic-correct?page=1

  public function index(){
    $perPosizioni =  PopolazioneNomadelfia::perPosizioni();
    $totale = PopolazioneNomadelfia::totalePopolazione();
    $gruppi = PopolazioneNomadelfia::gruppiComponenti();
    $posizioniFamiglia = PopolazioneNomadelfia::posizioneFamigliaCount();
    return view("nomadelfia.summary",compact('totale', 'perPosizioni', 'gruppi', 'posizioniFamiglia'));
  }

/*   SELECT posizioni.nome, count(*) as count
FROM persone
INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
INNER JOIN posizioni ON posizioni.id = persone_posizioni.posizione_id
WHERE persone.stato = '1'
group by posizioni.nome */

  public function printPersone(){


  }
  
  public function print(Request $request){
    $elenchi = collect($request->elenchi);
    if($elenchi->contains("personeeta")){
      $maggiorenni= Persona::attivo()->maggiorenni()->orderBy("nominativo");
      $maggiorenniDonne = Persona::attivo()->donne()->maggiorenni()->orderBy("nominativo");
      $minorenni = $this->getMinorenni();
      $minorenniCount = Persona::attivo()->minorenni()->count();
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
    $maggiorenni= Persona::attivo()->maggiorenni()->orderBy("nominativo");
    $maggiorenniDonne = Persona::attivo()->donne()->maggiorenni()->orderBy("nominativo");
    $minorenni = $this->getMinorenni();
    $minorenniCount = Persona::attivo()->minorenni()->count();
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
    $minorenni = Persona::attivo()->minorenni()->get();
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
