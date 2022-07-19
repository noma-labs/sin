<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\Models\PopolazioneNomadelfia;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScuolaController extends CoreBaseController
{

    public function index()
    {
        $anno = Anno::getLastAnno();
        $alunni = Studente::InAnnoScolastico($anno)->count();
        $cicloAlunni = Studente::InAnnoScolasticoPerCiclo($anno);
        dd($cicloAlunni);
        $resp = $anno->responsabile;
        return view('scuola.summary', compact('anno', 'cicloAlunni', 'alunni', 'resp'));
    }

    public function aggiungiClasse(Request $request, $id)
    {
        $validatedData = $request->validate([
            "tipo" => "required",
        ], [
            'tipo.required' => "Il tipo di classe da aggiungere è obbligatorio.",
        ]);
        $anno = Anno::FindOrFail($id);
        $classe = $anno->aggiungiClasse(ClasseTipo::findOrFail($request->tipo));
        return redirect()->back()->withSuccess("Classe  {$classe->tipo->nome} aggiunta a {{$anno->scolastico}} con successo.");
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

        $colStyle4Next = array('colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'nextColumn');
        $colStyle4NCont = array('colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'continuous');

        //$phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(8);
        $phpWord->setDefaultParagraphStyle(array(
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(2),
            'spacing' => 4
        ));

        // main page
        $anno = Anno::getLastAnno();

        $firstPage = $phpWord->addSection(array('vAlign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER));
        $firstPage->addText("Scuola Familiare di Nomadelfia", array('bold' => true, 'italic' => false, 'size' => 14));


        $firstPage->addText(Carbon::now()->toDatestring(), array('bold' => true, 'italic' => false, 'size' => 10),
            ['align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER]);
        $firstPage->addTextBreak(2);
        $r = is_null($anno->responsabile) ? "non asseganto" : $anno->responsabile->nominativo;
        $firstPage->addText("Responsabile: " . $r, array('bold' => true, 'italic' => false, 'size' => 10),
            ['align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER]);

        if ($elenchi->contains("studenti")) {
            $sc = $phpWord->addSection();
            $anno = Anno::getLastAnno();
            $sc->addTitle('Studenti ' . count($anno->alunni()), 1);

            $cicloAlunni = Studente::InAnnoScolasticoPerCiclo($anno);
            foreach ($cicloAlunni as $c) {
                $classeSect = $phpWord->addSection($colStyle4Next);
                $classeSect->addTitle(ucfirst($c->ciclo) . " " . $c->count, 1);
                if ($c->ciclo == "prescuola") {
                    $pre = $anno->prescuola();
//                    $classeSect->addText($el->tipo->nome . "  " . count($alunni));
                    foreach ($pre->alunni("data_nascita", "DESC")->get() as $alunno) {
                        $classeSect->addText($alunno->nominativo);
                    }
                }
                if ($c->ciclo == "elementari") {
                    $elemenatari = $anno->elementari();
                    foreach ($elemenatari as $el) {
                        $alunni = $el->alunni;
                        $classeSect->addTextBreak(1);
                        $classeSect->addTitle($el->tipo->nome . "  " . count($alunni), 2);
                        foreach ($alunni as $alunno) {
                            $classeSect->addText($alunno->nominativo);
                        }
                    }
                }
                if ($c->ciclo == "medie") {
                    $medie = $anno->medie();
                    foreach ($medie as $el) {
                        $alunni = $el->alunni;
                        $classeSect->addTextBreak(1);
                        $classeSect->addTitle($el->tipo->nome . "  " . count($alunni), 2);
                        foreach ($alunni as $alunno) {
                            $classeSect->addText($alunno->nominativo);
                        }
                    }
                }
                if ($c->ciclo == "superiori") {
                    $superiori = $anno->superiori();
                    foreach ($superiori as $el) {
                        $alunni = $el->alunni;
                        $classeSect->addTextBreak(1);
                        $classeSect->addTitle($el->tipo->nome . "  " . count($alunni), 2);
                        foreach ($alunni as $alunno) {
                            $classeSect->addText($alunno->nominativo);
                        }
                    }
                }
            }
        }
        if ($elenchi->contains("coordinatori")) {
            $coordinatori = $phpWord->addSection(array('vAlign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER));
            $coordinatori->addText("Responsabili dei cicli ed ambiti scolastici a.s. " . $anno->scolastico,
                array('bold' => true, 'italic' => false, 'size' => 14));

            $prescuola = $phpWord->addSection($colStyle4Next);
            $prescuola->addTitle("Prescuola ", 1);
            $cc = $anno->coordinatoriPrescuola();
            foreach ($cc as $classe => $coords) {
                $prescuola->addTextBreak(1);
                $prescuola->addTitle($classe, 2);
                foreach ($coords as $cord) {
                    $prescuola->addText($cord->nominativo );
                }
            }

            $elementari = $phpWord->addSection($colStyle4Next);
            $elementari->addTitle("Elementare - Scuola Primaria ", 1);
            $cc = $anno->coordinatoriElementari();
            foreach ($cc as $classe => $coords) {
                $elementari->addTextBreak(1);
                $elementari->addTitle($classe, 2);
                foreach ($coords as $cord) {
                    $elementari->addText($cord->nominativo);
                }
            }

            $medie = $phpWord->addSection($colStyle4Next);
            $medie->addTitle("Media - Scuola Secondaria di primo grado",1);
            $cc = $anno->coordinatoriMedie();
            foreach ($cc as $classe => $coords) {
                $medie->addTextBreak(1);
                $medie->addTitle($classe, 2);
                foreach ($coords as $cord) {
                    $medie->addText($cord->nominativo);
                }
            }

            $superiore = $phpWord->addSection($colStyle4Next);
            $superiore->addTitle("Superiori - Scuola Secondaria di secondo grado",1);
            $cc = $anno->coordinatorSuperiori();
            foreach ($cc as $classe => $coords) {
                $superiore->addTextBreak(1);
                $superiore->addTitle($classe, 2);
                foreach ($coords as $cord) {
                    $superiore->addText($cord->nominativo);
                }
            }

        }


        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "scuola-familiare-$data.docx";
        try {
            $objWriter->save(storage_path($file_name));
        } catch (\Exception $e) {
        }
        return response()->download(storage_path($file_name));
    }

}
