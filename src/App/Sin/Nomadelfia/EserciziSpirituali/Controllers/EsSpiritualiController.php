<?php

declare(strict_types=1);

namespace App\Nomadelfia\EserciziSpirituali\Controllers;

use Carbon;
use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TextAlignment;
use PhpOffice\PhpWord\SimpleType\VerticalJc;

final class EsSpiritualiController
{
    public function index()
    {
        $esercizi = EserciziSpirituali::attivi()->get();
        $noEsercizi = EserciziSpirituali::personeNoEsercizi();

        return view('nomadelfia.esercizi.index', compact('esercizi', 'noEsercizi'));
    }

    public function show(Request $request, $id)
    {
        $esercizio = EserciziSpirituali::findOrFail($id);
        $persone = $esercizio->personeOk();

        return view('nomadelfia.esercizi.show', compact('esercizio', 'persone'));
    }

    public function assegnaPersona(Request $request, $id)
    {
        $request->validate([
            'persona_id' => 'required',
        ], [
            'persona_id.required' => 'Persona è obbligatoria',
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
        $phpWord = new PhpWord;
        $phpWord->addTitleStyle(1, ['size' => 12, 'bold' => true, 'allCaps' => false], ['spaceAfter' => 240]);
        $phpWord->addTitleStyle(2, ['size' => 10, 'bold' => true], ['spaceBefore' => 240]);
        $phpWord->addTitleStyle(3, ['size' => 8, 'bold' => true]);

        $colStyle2Next = ['colsNum' => 2, 'colsSpace' => 700, 'breakType' => 'nextColumn'];
        $colStyle2Cont = ['colsNum' => 2, 'colsSpace' => 700, 'breakType' => 'continuous'];

        //$phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(8);
        $phpWord->setDefaultParagraphStyle([
            'spaceAfter' => Converter::pointToTwip(2),
            'spacing' => 4,
        ]);

        // main page
        $section = $phpWord->addSection(['vAlign' => VerticalJc::CENTER]);
        $section->addText(Carbon::now()->toDatestring(), ['bold' => true, 'italic' => false, 'size' => 16],
            ['align' => TextAlignment::CENTER]);
        $section->addTextBreak(2);
        $section->addText('Esercizi Spirituali ', ['bold' => true, 'italic' => false, 'size' => 14],
            ['align' => TextAlignment::CENTER]);
        $section->addTextBreak(2);

        $esercizi = EserciziSpirituali::attivi()->get();
        foreach ($esercizi as $esercizio) {
            $section = $phpWord->addSection($colStyle2Next);
            $section->addTitle($esercizio->turno, 1);
            $persone = $esercizio->personeOk();
            $uomini = $phpWord->addSection($colStyle2Cont);
            $uomini->addTitle('Uomini '.count($persone->uomini), 2);
            foreach ($persone->uomini as $value) {
                $uomini->addText(ucwords(mb_strtolower($value->nominativo)));
            }
            $donne = $phpWord->addSection($colStyle2Next);
            $donne->addTitle('Donne '.count($persone->donne), 2);
            foreach ($persone->donne as $value) {
                $donne->addText(ucfirst(mb_strtolower($value->nominativo)));
            }
        }
        // persone senza esercizi spirituali
        $phpWord->addSection();
        $noEsercizi = EserciziSpirituali::personeNoEsercizi();
        $uomini = $phpWord->addSection($colStyle2Cont);
        $uomini->addTitle('Senza esercizi Spirituali', 1);
        $uomini->addTitle('Uomini '.count($noEsercizi->uomini), 2);
        foreach ($noEsercizi->uomini as $value) {
            $uomini->addText(ucwords(mb_strtolower($value->nominativo)));
        }
        $donne = $phpWord->addSection($colStyle2Next);
        $donne->addTitle('Donne '.count($noEsercizi->donne), 2);
        foreach ($noEsercizi->donne as $value) {
            $donne->addText(ucfirst(mb_strtolower($value->nominativo)));
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "es-spirituali-$data.docx";
        $objWriter->save(storage_path($file_name));

        return response()->download(storage_path($file_name));
    }
}
