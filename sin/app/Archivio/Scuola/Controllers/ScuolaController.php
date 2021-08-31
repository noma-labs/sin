<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\Models\AnnoScolastico;
use App\Nomadelfia\Models\PopolazioneNomadelfia;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScuolaController extends CoreBaseController
{

    public function index()
    {
        $anno = Anno::getLastAnno();
        $cicloAlunni = $anno->totAlunniPerCiclo();
        $alunni = $anno->alunni();
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
        $firstPage->addText("Responsabili dei cicli ed ambiti scolastici a.s. " . $anno->scolastico,
            array('bold' => true, 'italic' => false, 'size' => 14));

        $firstPage->addText(Carbon::now()->toDatestring(), array('bold' => true, 'italic' => false, 'size' => 10),
            ['align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER]);
        $firstPage->addTextBreak(2);
        $r = is_null($anno->responsabile) ? "non asseganto" : $anno->responsabile->nominativo;
        $firstPage->addText("Responsabile: " . $r, array('bold' => true, 'italic' => false, 'size' => 10),
            ['align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER]);

        if ($elenchi->contains("studenti")) {
            $sc = $phpWord->addSection();
            $anno = Anno::getLastAnno();
            $sc->addTitle('Scuola ' . count($anno->alunni()), 1);

            $cicloAlunni = $anno->totAlunniPerCiclo();
            foreach ($cicloAlunni as $c) {
                $classeSect = $phpWord->addSection($colStyle4Next);
                $classeSect->addTitle(ucfirst($c->ciclo) . " " . $c->count, 1);
                if ($c->ciclo == "prescuola") {
                    $pre = $anno->prescuola();
//                    $classeSect->addText($el->tipo->nome . "  " . count($alunni));
                    foreach ($pre->alunni("data_nascita")->get() as $alunno) {
                        $classeSect->addText($alunno->nominativo);
                    }
                }
                if ($c->ciclo == "elementari") {
                    $elemenatari = $anno->elementari();
                    foreach ($elemenatari as $el) {
                        $alunni = $el->alunni;
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
                        $classeSect->addTitle($el->tipo->nome . "  " . count($alunni), 2);
                        foreach ($alunni as $alunno) {
                            $classeSect->addText($alunno->nominativo);
                        }
                    }
                }
            }
        }
        if ($elenchi->contains("coordinatori")) {
            $prescuola = $phpWord->addSection(array('vAlign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER));
            $prescuola->addText("PRESCUOLA ", array('bold' => true, 'italic' => false, 'size' => 14));

            $cc = $anno->coordinatoriPrescuola();
            foreach ($cc as $classe => $coords) {
                $prescuola->addText($classe . "    " . $coords->implode("nominativo", ","));
            }

            $elementari = $phpWord->addSection(array('vAlign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER));
            $elementari->addText("ELEMENTARI - Scuola Primaria ",
                array('bold' => true, 'italic' => false, 'size' => 12));
            $cc = $anno->coordinatoriElementari();
            foreach ($cc as $classe => $coords) {
                $elementari->addText($classe . "    " . $coords->implode("nominativo", ","));
            }

            $medie = $phpWord->addSection(array('vAlign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER));
            $medie->addText("MEDIE - Scuola Secondaria di primo grado",
                array('bold' => true, 'italic' => false, 'size' => 12));
            $cc = $anno->coordinatoriMedie();
            foreach ($cc as $classe => $coords) {
                $medie->addText($classe . "    " . $coords->implode("nominativo", ","));
            }

            $superiore = $phpWord->addSection(array('vAlign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER));
            $superiore->addText("SUPERIORI - Scuola Secondaria di secondo grado",
                array('bold' => true, 'italic' => false, 'size' => 12));
            $cc = $anno->coordinatorSuperiori();
            foreach ($cc as $classe => $coords) {
                $superiore->addText($classe . "    " . $coords->implode("nominativo", ","));
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
