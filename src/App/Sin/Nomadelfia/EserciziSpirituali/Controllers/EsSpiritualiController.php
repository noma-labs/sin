<?php

namespace App\Nomadelfia\EserciziSpirituali\Controllers;

use Illuminate\Http\Request;
use App\Core\Controllers\BaseController as CoreBaseController;
use App;
use Carbon;

use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\Persona\Models\Persona;

class EsSpiritualiController extends CoreBaseController
{
    public function index(Request $request)
    {
        $esercizi = EserciziSpirituali::attivi()->get();
        $noEsercizi = EserciziSpirituali::personeNoEsercizi();
        return view("nomadelfia.esercizi.index", compact('esercizi', 'noEsercizi'));
    }

    public function show(Request $request, $id)
    {
        $esercizio = EserciziSpirituali::findOrFail($id);
        $persone = $esercizio->personeOk();
        return view("nomadelfia.esercizi.show", compact('esercizio', 'persone'));
    }

    public function assegnaPersona(Request $request, $id)
    {
        $validatedData = $request->validate([
            "persona_id" => "required",
        ], [
            "persona_id.required" => "Persona è obbligatoria",
        ]);
        $persona = Persona::findOrFail($request->persona_id);
        $esercizi = EserciziSpirituali::findOrFail($id);
        $esercizi->aggiungiPersona($persona);
        return redirect()->back()->withSuccess("Persona $persona->nominativo aggiunta con successo.");
    }

    public function eliminaPersona(Request $request, $id, $idPersona)
    {
        $esercizio = EserciziSpirituali::findOrFail($id);
        $persona = Persona::findOrFail($idPersona);
        $esercizio->eliminaPersona($persona);
        return redirect()->back()->withSuccess("Persona $persona->nominativo eliminata con successo.");
    }

    public function stampa()
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        // define styles
        $fontStyle12 = array('size' => 10, 'spaceAfter' => 60);
        $phpWord->addTitleStyle(1, array('size' => 12, 'bold' => true, 'allCaps' => false), array('spaceAfter' => 240));
        $phpWord->addTitleStyle(2, array('size' => 10, 'bold' => true), array('spaceBefore' => 240));
        $phpWord->addTitleStyle(3, array('size' => 8, 'bold' => true));

        $colStyle2Next = array('colsNum' => 2, 'colsSpace' => 700, 'breakType' => 'nextColumn');
        $colStyle2Cont = array('colsNum' => 2, 'colsSpace' => 700, 'breakType' => 'continuous');

        $colStyle4Next = array('colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'nextColumn');
        $colStyle4NCont = array('colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'continuous');

        //$phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(8);
        $phpWord->setDefaultParagraphStyle(array(
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(2),
            'spacing' => 4
        ));


        // main page
        $section = $phpWord->addSection(array('vAlign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER));
        $section->addText(Carbon::now()->toDatestring(), array('bold' => true, 'italic' => false, 'size' => 16),
            ['align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER]);
        $section->addTextBreak(2);
        $section->addText("Esercizi Spirituali ", array('bold' => true, 'italic' => false, 'size' => 14),
            ['align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER]);
        $section->addTextBreak(2);

        $esercizi = EserciziSpirituali::attivi()->get();
        foreach ($esercizi as $esercizio) {
            $section = $phpWord->addSection($colStyle2Next);
            $section->addTitle($esercizio->turno, 1);
            $persone = $esercizio->personeOk();
            $uomini = $phpWord->addSection($colStyle2Cont);
            $uomini->addTitle('Uomini ' . count($persone->uomini), 2);
            foreach ($persone->uomini as $value) {
                $uomini->addText(ucwords(strtolower($value->nominativo)));
            }
            $donne = $phpWord->addSection($colStyle2Next);
            $donne->addTitle('Donne ' . count($persone->donne), 2);
            foreach ($persone->donne as $value) {
                $donne->addText(ucfirst(strtolower($value->nominativo)));
            }
        }
        // persone senza esercizi spirituali
        $section = $phpWord->addSection();
        $noEsercizi = EserciziSpirituali::personeNoEsercizi();
        $uomini = $phpWord->addSection($colStyle2Cont);
        $uomini->addTitle("Senza esercizi Spirituali", 1);
        $uomini->addTitle('Uomini ' . count($noEsercizi->uomini), 2);
        foreach ($noEsercizi->uomini as $value) {
            $uomini->addText(ucwords(strtolower($value->nominativo)));
        }
        $donne = $phpWord->addSection($colStyle2Next);
        $donne->addTitle('Donne ' . count($noEsercizi->donne), 2);
        foreach ($noEsercizi->donne as $value) {
            $donne->addText(ucfirst(strtolower($value->nominativo)));
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "es-spirituali-$data.docx";
        try {
            $objWriter->save(storage_path($file_name));
        } catch (Exception $e) {
        }
        return response()->download(storage_path($file_name));
    }
}