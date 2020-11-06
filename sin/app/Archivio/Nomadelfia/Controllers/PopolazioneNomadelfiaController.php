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
  public function index(){
    $totale = PopolazioneNomadelfia::totalePopolazione();
    $effettivi = PopolazioneNomadelfia::effettivi();
    $postulanti = PopolazioneNomadelfia::postulanti();
    $ospiti = PopolazioneNomadelfia::ospiti();
    $sacerdoti = PopolazioneNomadelfia::sacerdoti();
    $mvocazione = PopolazioneNomadelfia::mammeVocazione();
    $nomanamma = PopolazioneNomadelfia::nomadelfaMamma();
    $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni();
    $minorenni = PopolazioneNomadelfia::figliMinorenni();

    $figli = PopolazioneNomadelfia::byPosizione("FIGL");


    $gruppi = GruppoFamiliare::all();
    $posizioniFamiglia = PopolazioneNomadelfia::posizioneFamigliaCount();
    return view("nomadelfia.summary",compact('totale','effettivi', 'postulanti','ospiti', 'sacerdoti', 'mvocazione', 'nomanamma','maggiorenni', 'minorenni','figli', 'gruppi', 'posizioniFamiglia'));
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
      $gruppifamiliari = GruppoFamiliare::orderBy("nome");
    if($elenchi->contains("gruppi"))
      $gruppifamiliari = GruppoFamiliare::orderBy("nome");
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
    $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni();
    $maggiorenniUomini= $maggiorenni->uomini; 
    $maggiorenniDonne = $maggiorenni->donne;
    $minorenni = PopolazioneNomadelfia::Minorenni();
    $minorenni = collect($minorenni)->groupby(['anno', function($item){
                                                        return $item->sesso;
                                            }]);
    $minorenniCount = 0; // Persona::attivo()->minorenni()->count();
    $gruppifamiliari = GruppoFamiliare::orderBy("nome");
    $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");

    return view("nomadelfia.elenchi.popolazionenomadelfia", compact(
                                            "maggiorenniUomini",
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


  public function effettivi(Request $request){
    $effettivi = PopolazioneNomadelfia::effettivi();
    return view("nomadelfia.popolazione.effettivi",compact('effettivi'));
  }

  public function postulanti(Request $request){
    $postulanti = PopolazioneNomadelfia::postulanti();
    return view("nomadelfia.popolazione.postulanti",compact('postulanti'));
  }

  public function ospiti(Request $request){
    $ospiti = PopolazioneNomadelfia::ospiti();
    return view("nomadelfia.popolazione.ospiti",compact('ospiti'));
  }

  public function figliMaggiorenni(Request $request){
    $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni();
    return view("nomadelfia.popolazione.figlimaggiorenni",compact('maggiorenni'));
  }

  public function figliMinorenni(Request $request){
    $minorenni = PopolazioneNomadelfia::figliMinorenni();
    return view("nomadelfia.popolazione.figliminorenni",compact('minorenni'));
  }

  public function figli(Request $request){
    $postulanti = collect(PopolazioneNomadelfia::figli())->sortBy("data_nascita");
    $count = $postulanti->count();
    $g =  $postulanti->groupBy("sesso");
    $uomini = $g->get('M');
    $donne = $g->get('F');
    return view("nomadelfia.popolazione.figli",compact('count', $count,'uomini', $uomini, "donne",$donne));
  }
  

}
