<?php
namespace App\Nomadelfia\Controllers;
use PDF;
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
    set_time_limit(0);
    $persone = Persona::all();
    $totale = Persona::Presente()->count();
    $maggiorenniUomini = Persona::presente()->uomini()->maggiorenni()->orderBy("nominativo");
    $maggiorenniDonne = Persona::presente()->donne()->maggiorenni()->orderBy("nominativo");
    $minorenni = Persona::presente()->minorenni()->get();
    $minorenniCount = $minorenni->count();
    $minorenni->map(function($item,$key){
      return $item['anno'] = Carbon::parse($item['data_nascita_persona'])->year;
    });
    $groupMinorenni= $minorenni->sortBy(function ($persona, $key) {
        return $persona['anno'];
    });
    $minorenni = $groupMinorenni->groupby(['anno', 
                      function($item){
                        return $item['sesso'];
                      }]);

    $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->orderBy("nome");
    $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");

    $pdf = PDF::loadView("nomadelfia.print",compact('totale',
                                                      "maggiorenniUomini",
                                                      "maggiorenniDonne",
                                                      "minorenni",
                                                      "minorenniCount",
                                                      "gruppifamiliari",
                                                      "aziende"));
    $data = Carbon::now();
    return $pdf->setPaper('a4')->download("etichette-$data.pdf"); //stream
  }

  public function preview(){
    $persone = Persona::all();
    $totale = Persona::Presente()->count();
    $maggiorenniUomini = Persona::presente()->uomini()->maggiorenni()->orderBy("nominativo");
    $maggiorenniDonne = Persona::presente()->donne()->maggiorenni()->orderBy("nominativo");
    $minorenni = Persona::presente()->minorenni()->get();
    $minorenniCount = $minorenni->count();
    $minorenni->map(function($item,$key){
      return $item['anno'] = Carbon::parse($item['data_nascita_persona'])->year;
    });
    $groupMinorenni= $minorenni->sortBy(function ($persona, $key) {
        return $persona['anno'];
    });
    $minorenni = $groupMinorenni->groupby(['anno', 
                      function($item){
                        return $item['sesso'];
                      }]);

    $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->orderBy("nome");
    $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");

    return view("nomadelfia.print",compact('totale',
                                            "maggiorenniUomini",
                                            "maggiorenniDonne",
                                            "minorenni",
                                            "minorenniCount",
                                            "gruppifamiliari",
                                            "aziende"));
  }

}
