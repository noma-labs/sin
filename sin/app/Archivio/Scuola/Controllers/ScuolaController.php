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
        $alunni = $anno->alunni();
        $resp = $anno->responsabile;
        return view('scuola.summary', compact('anno', 'alunni', 'resp'));
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
        $firstPage->addText("Responsabili dei cicli ed ambiti scolastici a.s. " .$anno->scolastico , array('bold' => true, 'italic' => false, 'size' => 14));

        $firstPage->addText(Carbon::now()->toDatestring(), array('bold' => true, 'italic' => false, 'size' => 10),  ['align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER]);
        $firstPage->addTextBreak(2);
        $r = is_null($anno->responsabile)  ? "non asseganto" :  $anno->responsabile->nominativo;
        $firstPage->addText("Responsabile: ".$r ,array('bold' => true, 'italic' => false, 'size' => 10),
            ['align' => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER]);

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
