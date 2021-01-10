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
    public function index()
    {
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
        return view("nomadelfia.summary", compact('totale', 'effettivi', 'postulanti', 'ospiti', 'sacerdoti', 'mvocazione', 'nomanamma', 'maggiorenni', 'minorenni', 'figli', 'gruppi', 'posizioniFamiglia'));
    }

    public function show(Request $request)
    {
        $popolazione = PopolazioneNomadelfia::popolazione();
        return view("nomadelfia.popolazione.show", compact('popolazione'));
    }

    public function effettivi(Request $request)
    {
        $effettivi = PopolazioneNomadelfia::effettivi();
        return view("nomadelfia.popolazione.effettivi", compact('effettivi'));
    }

    public function postulanti(Request $request)
    {
        $postulanti = PopolazioneNomadelfia::postulanti();
        return view("nomadelfia.popolazione.postulanti", compact('postulanti'));
    }

    public function ospiti(Request $request)
    {
        $ospiti = PopolazioneNomadelfia::ospiti();
        return view("nomadelfia.popolazione.ospiti", compact('ospiti'));
    }

    public function sacerdoti(Request $request)
    {
        $sacerdoti = PopolazioneNomadelfia::sacerdoti();
        return view("nomadelfia.popolazione.sacerdoti", compact('sacerdoti'));
    }
  
    public function mammeVocazione(Request $request)
    {
        $mvocazione = PopolazioneNomadelfia::mammeVocazione();
        return view("nomadelfia.popolazione.mammevocazione", compact('mvocazione'));
    }

    public function nomadelfaMamma(Request $request)
    {
        $nmamma = PopolazioneNomadelfia::nomadelfaMamma();
        return view("nomadelfia.popolazione.nomadelfamamma", compact('nmamma'));
    }

    public function figliMaggiorenni(Request $request)
    {
        $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni();
        return view("nomadelfia.popolazione.figlimaggiorenni", compact('maggiorenni'));
    }

    public function figliMinorenni(Request $request)
    {
        $minorenni = PopolazioneNomadelfia::figliMinorenni();
        return view("nomadelfia.popolazione.figliminorenni", compact('minorenni'));
    }
  
  
    public function print(Request $request)
    {
        /*
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
*/
        $elenchi = collect($request->elenchi);
        if ($elenchi->contains("personeeta")) {
            //$maggiorenni= Persona::attivo()->maggiorenni()->orderBy("nominativo");
            $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni();
            $maggiorenniDonne = Persona::attivo()->donne()->maggiorenni()->orderBy("nominativo");
            $minorenni = $this->getMinorenni();
            $minorenniCount = Persona::attivo()->minorenni()->count();
        }
    
        if ($elenchi->contains("personestati")) {
            $personestati = true;
        }
        if ($elenchi->contains("personeposizioni")) {
            $personeposizioni = true;
        }
        if ($elenchi->contains("famiglie")) {
            $gruppifamiliari = GruppoFamiliare::orderBy("nome");
        }
        if ($elenchi->contains("gruppi")) {
            $gruppifamiliari = GruppoFamiliare::orderBy("nome");
        }
        if ($elenchi->contains("aziende")) {
            $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");
        }
        if ($elenchi->contains("scuola")) {
            $scuola = [];
        }


        $pdf = SnappyPdf::loadView("nomadelfia.elenchi.popolazionenomadelfia", compact(
            'totale',
            "maggiorenni",
            "maggiorenniDonne",
            "minorenni",
            "minorenniCount",
            "personestati",
            "personeposizioni",
            "gruppifamiliari",
            "aziende"
        ));
        // viewport-size must be set otherwise the pdf will be bad formatted
        $pdf->setOption('viewport-size', '1280x1024');
        $data = Carbon::now();
        return $pdf->download("popolazione-$data.pdf"); //stream
    }

    public function preview()
    {
        /*
        $totale = PopolazioneNomadelfia::totalePopolazione();

        $ospiti = PopolazioneNomadelfia::ospiti();

        $nomanamma = PopolazioneNomadelfia::nomadelfaMamma();
        $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni();
        $minorenni = PopolazioneNomadelfia::figliMinorenni();
        $figli = PopolazioneNomadelfia::byPosizione("FIGL");
*/

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
      
        // Normal
        $section = $phpWord->addSection();
        $section->addText("TUTTI");

        // Two columns
        $maggUomini = $phpWord->addSection(
            array(
                'colsNum'   => 2,
                'colsSpace' => 300,
                'breakType' => 'continuous',
            )
        );

        $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni('nominativo');
        $maggUomini->addText("Uomini Maggiorenne". count($maggiorenni->uomini));
        foreach ($maggiorenni->uomini as $value) {
            $maggUomini->addText(" {$value->nominativo}");
        }

        
        $maggUomini->addPageBreak();
        $maggUomini->addText("Donne Maggiorenne". count($maggiorenni->donne));
        foreach ($maggiorenni->donne as $value) {
            $maggUomini->addText(" {$value->nominativo}");
        }

        // Normal
        //$section->addText("POSZIONIE NOMADELFIE");

        $effettiviUomini = $phpWord->addSection(
            array(
                'colsNum'   => 2,
                'colsSpace' => 300,
                'breakType' => 'nextColumn',
            )
        );
        $effettivi = PopolazioneNomadelfia::effettivi();
        $effettiviUomini->addText("Uomini Effettivi". count($effettivi->uomini));
        foreach ($effettivi->uomini as $value) {
            $effettiviUomini->addText(" {$value->nominativo}");
        }
        
        
        $effettiviDonne = $phpWord->addSection(
            array(
                'colsNum'   => 2,
                'colsSpace' => 300,
                'breakType' => 'nextColumn',
            )
        );
        $effettiviDonne->addText("Donne Effettivi". count($effettivi->donne));
        foreach ($effettivi->donne as $value) {
            $effettiviDonne->addText(" {$value->nominativo}");
        }
        /*
                $sacerdoti = PopolazioneNomadelfia::sacerdoti();
                $section->addTextBreak(2);
                $posizioni->addText("Sacerdoti". count($sacerdoti));
                foreach ($sacerdoti as $value) {
                    $posizioni->addText(" {$value->nominativo}");
                }

                $mvocazione = PopolazioneNomadelfia::mammeVocazione();
                $posizioni->addText("Mamme Vocazione". count($mvocazione));
                foreach ($mvocazione as $value) {
                    $posizioni->addText(" {$value->nominativo}");
                }
        */
        /* Normal
        $section = $phpWord->addSection(array('breakType' => 'continuous'));
        $section->addText("Normal paragraph again. {$filler}");

        // Three columns
        $section = $phpWord->addSection(
            array(
        'colsNum'   => 3,
        'colsSpace' => 720,
        'breakType' => 'continuous',
    )
        );
        $section->addText("Three columns, half inch (720 twips) spacing. {$filler}");

        // Normal
        $section = $phpWord->addSection(array('breakType' => 'continuous'));
        $section->addText("Normal paragraph again. {$filler}");
        */
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save(storage_path('helloWorld.docx'));
        } catch (Exception $e) {
        }


        return response()->download(storage_path('helloWorld.docx'));
        

        $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni('nominativo');
        $minorenni = PopolazioneNomadelfia::figliMinorenniPerAnno();

        $effettivi = PopolazioneNomadelfia::effettivi();
        $sacerdoti = PopolazioneNomadelfia::sacerdoti();
        $mvocazione = PopolazioneNomadelfia::mammeVocazione();

        $postulanti = PopolazioneNomadelfia::postulanti();
        $ospiti = PopolazioneNomadelfia::ospiti();
        $fra1821= PopolazioneNomadelfia::figliFra18e21();
        $mag21 = PopolazioneNomadelfia::figliMaggiori21();

        $gruppifamiliari = GruppoFamiliare::orderBy("nome");
        $aziende = Azienda::with("lavoratoriAttuali")->orderBy("nome_azienda");

        $personestati = true;
        $personeposizioni = true;

        return view("nomadelfia.elenchi.popolazionenomadelfia", compact(
            "maggiorenni",
            "minorenni",
            "personestati",
            "personeposizioni",
            "effettivi",
            "postulanti",
            "ospiti",
            "fra1821",
            "mag21",
            "sacerdoti",
            "mvocazione",
            "minorenniCount",
            "gruppifamiliari",
            "aziende"
        ));
    }


    public function getMinorenni()
    {
        $minorenni = Persona::attivo()->minorenni()->get();
        $minorenni->map(function ($item, $key) {
            return $item['anno'] = Carbon::parse($item['data_nascita'])->year;
        });
        $groupMinorenni= $minorenni->sortBy(function ($persona, $key) {
            return $persona['anno'];
        });
        return $groupMinorenni->groupby(['anno',
                      function ($item) {
                          return $item['sesso'];
                      }]);
    }
}
