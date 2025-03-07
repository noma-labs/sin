<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TextAlignment;
use PhpOffice\PhpWord\SimpleType\VerticalJc;

final class ScuolaController
{
    public function summary()
    {
        $lastAnno = Anno::getLastAnno();
        $alunni = Studente::InAnnoScolastico($lastAnno)->count();
        $cicloAlunni = Studente::InAnnoScolasticoPerCiclo($lastAnno)->get();

        $resp = $lastAnno->responsabile;

        return view('scuola.summary', compact('lastAnno', 'alunni', 'cicloAlunni', 'resp'));
    }

    public function storico()
    {
        $anni = Anno::leftJoin('classi', 'classi.anno_id', '=', 'anno.id')
            ->leftJoin('alunni_classi', 'alunni_classi.classe_id', '=', 'classi.id')
            ->select('anno.id', 'anno.scolastico', DB::raw('COUNT(alunni_classi.persona_id) as alunni_count'))
            ->groupBy('anno.id', 'anno.scolastico')
            ->orderBy('anno.scolastico', 'DESC')
            ->withoutGlobalScope('order')
            ->get();

        return view('scuola.anno.storico', compact('anni'));
    }

    public function print(Request $request)
    {
        $elenchi = collect($request->elenchi);

        $phpWord = new PhpWord;
        $phpWord->addTitleStyle(1, ['size' => 12, 'bold' => true, 'allCaps' => true], ['spaceAfter' => 240]);
        $phpWord->addTitleStyle(2, ['size' => 10, 'bold' => true]);
        $phpWord->addTitleStyle(3, ['size' => 8, 'bold' => true]); // stile per le famiglie

        $colStyle4Next = ['colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'nextColumn'];
        $phpWord->addSection(['breakType' => 'continuous', 'colsNum' => 2]);

        // $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(8);
        $phpWord->setDefaultParagraphStyle([
            'spaceAfter' => Converter::pointToTwip(2),
            'spacing' => 4,
        ]);

        // main page
        $anno = Anno::getLastAnno();

        $firstPage = $phpWord->addSection(['vAlign' => VerticalJc::CENTER]);
        $firstPage->addText('Scuola Familiare di Nomadelfia', ['bold' => true, 'italic' => false, 'size' => 14]);

        $firstPage->addText(Carbon::now()->toDatestring(), ['bold' => true, 'italic' => false, 'size' => 10],
            ['align' => TextAlignment::CENTER]);
        $firstPage->addTextBreak(2);
        $r = is_null($anno->responsabile) ? 'non asseganto' : $anno->responsabile->nominativo;
        $firstPage->addText('Responsabile: '.$r, ['bold' => true, 'italic' => false, 'size' => 10],
            ['align' => TextAlignment::CENTER]);

        if ($elenchi->contains('studenti')) {
            $sc = $phpWord->addSection();
            $sc->addTitle('Studenti '.Studente::InAnnoScolastico($anno)->count(), 1);

            //            $cicloAlunni = Studente::InAnnoScolasticoPerCiclo($anno)->get();

            $classeSect = $phpWord->addSection($colStyle4Next);
            $pre = $anno->prescuola();
            $classeSect->addTitle('Prescuola ', 1); //  . $pre->alunni('data_nascita', 'DESC')->count(), 1);
            foreach ($pre->alunni('data_nascita', 'DESC')->get() as $alunno) {
                $classeSect->addText($alunno->nominativo);
            }

            $classeSect = $phpWord->addSection($colStyle4Next);
            $elemenatari = $anno->elementari();
            $classeSect->addTitle('Elementari ', 1); // . $elemenatari->alunni('data_nascita', 'DESC')->count(), 1);
            foreach ($elemenatari as $el) {
                $alunni = $el->alunni;
                $classeSect->addTextBreak(1);
                $classeSect->addTitle($el->tipo->nome.'  '.count($alunni), 2);
                foreach ($alunni as $alunno) {
                    $classeSect->addText($alunno->nominativo);
                }
            }

            $classeSect = $phpWord->addSection($colStyle4Next);
            $medie = $anno->medie();
            $classeSect->addTitle('Medie ', 1); // . $elemenatari->alunni('data_nascita', 'DESC')->count(), 1);
            foreach ($medie as $el) {
                $alunni = $el->alunni;
                $classeSect->addTextBreak(1);
                $classeSect->addTitle($el->tipo->nome.'  '.count($alunni), 2);
                foreach ($alunni as $alunno) {
                    $classeSect->addText($alunno->nominativo);
                }
            }

            $classeSect = $phpWord->addSection($colStyle4Next);
            $superiori = $anno->superiori();
            $classeSect->addTitle('Superiori ', 1); //
            foreach ($superiori as $el) {
                $alunni = $el->alunni;
                $classeSect->addTextBreak(1);
                $classeSect->addTitle($el->tipo->nome.'  '.count($alunni), 2);
                foreach ($alunni as $alunno) {
                    $classeSect->addText($alunno->nominativo);
                }
            }
        }

        if ($elenchi->contains('coordinatori')) {
            $coordinatori = $phpWord->addSection(['vAlign' => VerticalJc::CENTER]);
            $coordinatori->addText('Responsabili dei cicli ed ambiti scolastici a.s. '.$anno->scolastico,
                ['bold' => true, 'italic' => false, 'size' => 14]);

            $prescuola = $phpWord->addSection($colStyle4Next);
            $prescuola->addTitle('Prescuola ', 1);
            $cc = $anno->coordinatoriPrescuola();
            foreach ($cc as $classe => $coords) {
                $prescuola->addTextBreak(1);
                $prescuola->addTitle($classe, 2);
                foreach ($coords as $cord) {
                    $prescuola->addText($cord->nominativo);
                }
            }

            $elementari = $phpWord->addSection($colStyle4Next);
            $elementari->addTitle('Elementare - Scuola Primaria ', 1);
            $cc = $anno->coordinatoriElementari();
            foreach ($cc as $classe => $coords) {
                $elementari->addTextBreak(1);
                $elementari->addTitle($classe, 2);
                foreach ($coords as $cord) {
                    $elementari->addText($cord->nominativo);
                }
            }

            $medie = $phpWord->addSection($colStyle4Next);
            $medie->addTitle('Media - Scuola Secondaria di primo grado', 1);
            $cc = $anno->coordinatoriMedie();
            foreach ($cc as $classe => $coords) {
                $medie->addTextBreak(1);
                $medie->addTitle($classe, 2);
                foreach ($coords as $cord) {
                    $medie->addText($cord->nominativo);
                }
            }

            $superiore = $phpWord->addSection($colStyle4Next);
            $superiore->addTitle('Superiori - Scuola Secondaria di secondo grado', 1);
            $cc = $anno->coordinatorSuperiori();
            foreach ($cc as $classe => $coords) {
                $superiore->addTextBreak(1);
                $superiore->addTitle($classe, 2);
                foreach ($coords as $cord) {
                    $superiore->addText($cord->nominativo);
                }
            }

        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "scuola-familiare-$data.docx";
        $objWriter->save(storage_path($file_name));

        return response()->download(storage_path($file_name));
    }
}
