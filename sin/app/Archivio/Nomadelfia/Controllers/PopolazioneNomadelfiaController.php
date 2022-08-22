<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\EserciziSpirituali;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Incarico;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\PopolazioneNomadelfia;
use App\Scuola\Models\Anno;
use App\Traits\SortableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SnappyPdf;
use Validator;
use Carbon;

class PopolazioneNomadelfiaController extends CoreBaseController
{

    public function index()
    {
        $totale = PopolazioneNomadelfia::totalePopolazione();
        $maggiorenni = PopolazioneNomadelfia::maggiorenni();
        $effettivi = PopolazioneNomadelfia::effettivi();
        $postulanti = PopolazioneNomadelfia::postulanti();
        $ospiti = PopolazioneNomadelfia::ospiti();
        $sacerdoti = PopolazioneNomadelfia::sacerdoti();
        $mvocazione = PopolazioneNomadelfia::mammeVocazione();
        $nomanamma = PopolazioneNomadelfia::nomadelfaMamma();
        $figliMaggiorenni = PopolazioneNomadelfia::figliMaggiorenni();
        $minorenni = PopolazioneNomadelfia::figliMinorenni();


        $figli = PopolazioneNomadelfia::byPosizione("FIGL");

        $gruppi =  GruppoFamiliare::countComponenti();
        $posizioniFamiglia = PopolazioneNomadelfia::posizioneFamigliaCount();
        $famiglieNumerose = Famiglia::famiglieNumerose();

        return view("nomadelfia.summary", compact('totale', 'maggiorenni', 'effettivi', 'postulanti', 'ospiti', 'sacerdoti', 'mvocazione', 'nomanamma', 'figliMaggiorenni', 'minorenni', 'figli', 'gruppi', 'posizioniFamiglia','famiglieNumerose'));
    }

    public function show(Request $request)
    {
        $popolazione = PopolazioneNomadelfia::popolazione();
        $stats = PopolazioneNomadelfia::stats();
//        dd($stats);
        return view("nomadelfia.popolazione.show", compact('popolazione', 'stats'));
    }

    public function maggiorenni(Request $request)
    {
        $maggiorenni = PopolazioneNomadelfia::maggiorenni("nominativo");
        // TODO: togliere da qui. messo solo per urgenza di creare es spirituali
        $esercizi = EserciziSpirituali::attivi()->get();
        return view("nomadelfia.popolazione.maggiorenni", compact('maggiorenni', 'esercizi'));
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
        $elenchi = collect($request->elenchi);
    
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        // define styles
        $fontStyle12 = array('size' => 10, 'spaceAfter' => 60);
        $phpWord->addTitleStyle(1, array('size' => 12, 'bold' => true, 'allCaps' => true), array('spaceAfter' => 240));
        $phpWord->addTitleStyle(2, array('size' => 10, 'bold' => true,));
        $phpWord->addTitleStyle(3, array('size' => 8, 'bold' => true)); //stile per le famiglie

        $colStyle4Next = array('colsNum'   => 4,'colsSpace' => 300,'breakType' => 'nextColumn' );
        $colStyle4NCont = array('colsNum'   => 4,'colsSpace' => 300,'breakType' => 'continuous' );

        //$phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(8);
        $phpWord->setDefaultParagraphStyle(array('spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(2), 'spacing' => 4 ));

        // main page
        $section = $phpWord->addSection(array('vAlign'=>\PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER));
        $section->addText(Carbon::now()->toDatestring(), array('bold'=>true, 'italic'=>false, 'size'=>16), [ 'align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER ]);
        $section->addTextBreak(2);
        $section->addText("POPOLAZIONE DI NOMADELFIA", array('bold'=>true, 'italic'=>false, 'size'=>14), [ 'align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER ]);
        $section->addTextBreak(2);
        $section->addText("Totale ". PopolazioneNomadelfia::totalePopolazione(), array('bold'=>true, 'italic'=>false, 'size'=>12), [ 'align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER ]);

        // Add TOC #1
        $section = $phpWord->addSection();
        $section->addTitle('Indice', 0);
        $section->addTextBreak(2);
        $toc = $section->addTOC($fontStyle12);
        $toc->setMaxDepth(2);
        $section->addTextBreak(2);

        // Section maggiorenni
        if ($elenchi->contains("maggMin")) {
            $section->addPageBreak();
            $maggiorenni = PopolazioneNomadelfia::maggiorenni();
            $section = $phpWord->addSection();
            $section->addTitle('Maggiorenni '. $maggiorenni->total, 1);
    
            $sectMaggUomini = $phpWord->addSection($colStyle4NCont);
            $sectMaggUomini->addTitle("Uomini ". count($maggiorenni->uomini), 2);
            foreach ($maggiorenni->uomini as $value) {
                $sectMaggUomini->addText($value->nominativo);
          }
            $maggDonne = $phpWord->addSection($colStyle4Next);
            $maggDonne->addTitle("Donne ". count($maggiorenni->donne), 2);
            foreach ($maggiorenni->donne as $value) {
                $maggDonne->addText($value->nominativo);
          }
            // Figli minorenni
            $minorenni = PopolazioneNomadelfia::figliMinorenni();
            $section = $phpWord->addSection()->addTitle('Figli Minorenni '. $minorenni->total, 1);
            $sectMinorenniUomini = $phpWord->addSection($colStyle4NCont);
            $sectMinorenniUomini->addTitle("Uomini ". count($minorenni->uomini), 2);
            foreach ($minorenni->uomini as $value) {
                $sectMinorenniUomini->addText($value->nominativo);
          }
            $maggDonne = $phpWord->addSection($colStyle4Next);
            $maggDonne->addTitle("Donne ". count($minorenni->donne), 2);
            foreach ($minorenni->donne as $value) {
                $maggDonne->addText($value->nominativo);
          }
        }
    
        if ($elenchi->contains("effePostOspFig")) {
            
        // Effettivi
            $effettivi = PopolazioneNomadelfia::effettivi();
            $section = $phpWord->addSection()->addTitle('Effettivi '. $effettivi->total, 1);

            $effeUomini = $phpWord->addSection($colStyle4NCont);
            $effeUomini->addTitle("Uomini ". count($effettivi->uomini), 2);
            foreach ($effettivi->uomini as $value) {
                $effeUomini->addText($value->nominativo);
          }
            $effeDonne = $phpWord->addSection($colStyle4Next);
            $effeDonne->addTitle("Donne ". count($effettivi->donne), 2);
            foreach ($effettivi->donne as $value) {
                $effeDonne->addText($value->nominativo);
          }

            // Postulanti
            $postulanti = PopolazioneNomadelfia::postulanti();
            $postSect = $phpWord->addSection($colStyle4Next);
            $postSect->addTitle('Postulanti '. $postulanti->total, 1);
          
            $postSect->addTitle('Uomini '. count($postulanti->uomini), 2);
            foreach ($postulanti->uomini as $value) {
                $postSect->addText($value->nominativo);
          }
            $postSect->addTitle('Donne '. count($postulanti->donne), 2);
            foreach ($postulanti->donne as $value) {
                $postSect->addText($value->nominativo);
          }

            // Ospiti
            $ospiti = PopolazioneNomadelfia::ospiti();
            $postSect = $phpWord->addSection($colStyle4Next);
            $postSect->addTitle('Ospiti '. $ospiti->total, 1);
            $postSect->addTitle('Uomini '. count($ospiti->uomini), 2);
            foreach ($ospiti->uomini as $value) {
                $postSect->addText($value->nominativo);
          }
            $postSect->addTitle('Donne '. count($ospiti->donne), 2);
            foreach ($ospiti->donne as $value) {
                $postSect->addText($value->nominativo);
          }

            // Sacerdoti
            $sacerdoti = PopolazioneNomadelfia::sacerdoti();
            $sacSect = $phpWord->addSection($colStyle4Next);
            $sacSect->addTitle('Sacerdoti '. count($sacerdoti), 2);
            foreach ($sacerdoti as $value) {
                $sacSect->addText($value->nominativo);
          }

            // Mamme di vocazione
            $mvocazione = PopolazioneNomadelfia::mammeVocazione();
            $mvocSect = $phpWord->addSection($colStyle4Next);
            $mvocSect->addTitle('Mamme Di Vocazione '. count($mvocazione), 2);
            foreach ($mvocazione as $value) {
                $mvocSect->addText($value->nominativo);
          }

            // Figli >21
            $figliMag21 = PopolazioneNomadelfia::figliMaggiori21();
            $figlMagSect = $phpWord->addSection($colStyle4Next);
            $figlMagSect->addTitle('Figli/e >21 '. $figliMag21->total, 1);
            $figlMagSect->addTitle('Figli '. count($figliMag21->uomini), 2);
            foreach ($figliMag21->uomini as $value) {
                $figlMagSect->addText($value->nominativo);
          }
            $figlMagSect->addTitle('Figlie '. count($figliMag21->donne), 2);
            foreach ($figliMag21->donne as $value) {
                $figlMagSect->addText($value->nominativo);
          }
         
            // Figli 18-21
            $figli21 = PopolazioneNomadelfia::figliFra18e21();
            $figliSect = $phpWord->addSection($colStyle4Next);
            $figliSect->addTitle('Figli/e 18...21 '. $figli21->total, 1);
            $figliSect->addTitle('Figli '. count($figli21->uomini), 2);
            foreach ($figli21->uomini as $value) {
                $figliSect->addText($value->nominativo);
          }
            $figliSect->addTitle('Figlie '. count($figli21->donne), 2);
            foreach ($figli21->donne as $value) {
                $figliSect->addText($value->nominativo);
          }
        }
        if ($elenchi->contains("famiglie")) {
            // Famiglie
            $famiglie = PopolazioneNomadelfia::famiglie();
            $famiglieSect = $phpWord->addSection($colStyle4NCont);
            $famiglieSect->addPageBreak();
            $famiglieSect->addTitle('Famiglie '. count($famiglie), 1);
            foreach ($famiglie as $id => $componenti) {
                $famiglieSect->addTextBreak(1);
                foreach ($componenti as $componente) {
                    if (!Str::startsWith($componente->posizione_famiglia, 'FIGLIO')) {
                        $famiglieSect->addTitle($componente->nominativo, 3);
                    } else {
                        $year = Carbon::parse($componente->data_nascita)->year;
                        $famiglieSect->addText("    ".$year." ".$componente->nominativo);
                    }
                }
            }
        }
        if ($elenchi->contains("gruppi")) {
            // gruppi familiari
            // $gruppiSect = $phpWord->addSection();
            // $gruppiSect->addTitle('Gruppi Familiari ', 1);

            foreach (GruppoFamiliare::orderby("nome")->get() as $gruppo) {
                $gruppiSect = $phpWord->addSection($colStyle4Next);
                $gruppiSect->addTitle($gruppo->nome. " ".$gruppo->personeAttuale()->count(), 2);
     
                foreach ($gruppo->Single() as $single) {
                    $gruppiSect->addTitle($single->nominativo, 3);
                }
                foreach ($gruppo->Famiglie() as $famiglia_id => $componenti) {
                    $gruppiSect->addTextBreak(1);
                    foreach ($componenti as $componente) {
                        if (!Str::startsWith($componente->posizione_famiglia, 'FIGLIO')) {
                            $gruppiSect->addText($componente->nominativo, array('bold'  => true));
                        } else {
                            $year = Carbon::parse($componente->data_nascita)->year;
                            $gruppiSect->addText("    ".$year." ".$componente->nominativo);
                        }
                    }
                }
            }
        }
        if ($elenchi->contains("aziende")) {
            // Aziende
            $azi = $phpWord->addSection();
            $azi->addTitle('Aziende ', 1);
            $sectAziende = $phpWord->addSection($colStyle4NCont);
            foreach (Azienda::aziende()->get() as $azienda) {
                $sectAziende->addTextBreak(1);
                $lavoratori = $azienda->lavoratoriAttuali()->get();
                $sectAziende->addTitle($azienda->nome_azienda. "  ". count($lavoratori), 3);
                foreach ($lavoratori as $lavoratore) {
                    $sectAziende->addText("    ".$lavoratore->nominativo);
                }
            }
        }
        if ($elenchi->contains("incarichi")) {
            // Incarichi
            $azi = $phpWord->addSection();
            $incarichi = Incarico::all();
            $azi->addTitle('Incarichi '.$incarichi->count() , 1);
            $sectAziende = $phpWord->addSection($colStyle4NCont);
            foreach ($incarichi as $incarico) {
                $sectAziende->addTextBreak(1);
                $lavoratori = $incarico->lavoratoriAttuali()->get();
                $sectAziende->addTitle($incarico->nome. "  ". count($lavoratori), 3);
                foreach ($lavoratori as $lavoratore) {
                    $sectAziende->addText("    ".$lavoratore->nominativo);
                }
            }
        }
        if ($elenchi->contains("scuola")) {
            $sc = $phpWord->addSection();
            $anno = Anno::getLastAnno();
            $sc->addTitle('Scuola '. count($anno->alunni()), 1);

            $classeSect = $phpWord->addSection($colStyle4NCont);
            foreach ($anno->classi()->get() as $classe) {
                $alunni = $classe->alunni();
                if ($alunni->count() > 0 ){
                    $classeSect->addTextBreak(1);
                    $classeSect->addTitle($classe->tipo->nome. " ". $alunni->count(), 2);
                    foreach ($classe->alunni()->get() as $alunno) {
                        $year = Carbon::parse($alunno->data_nascita)->year;
                        $classeSect->addText("    " . $year . " " . $alunno->nominativo);
                    }
                }
                }

        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "popolazione-$data.docx";
        try {
            $objWriter->save(storage_path($file_name));
        } catch (Exception $e) {
        }
        return response()->download(storage_path($file_name));
    }

    public function preview()
    {
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
