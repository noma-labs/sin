<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\ExportPopolazioneData;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TextAlignment;
use PhpOffice\PhpWord\SimpleType\VerticalJc;

class ExportPopolazioneToWordAction
{
    public function execute(Collection $elenchi): PhpWord
    {
        $data = new ExportPopolazioneData();

        $phpWord = new PhpWord();
        // define styles
        $fontStyle12 = ['size' => 10, 'spaceAfter' => 60];
        $phpWord->addTitleStyle(1, ['size' => 12, 'bold' => true, 'allCaps' => true], ['spaceAfter' => 240]);
        $phpWord->addTitleStyle(2, ['size' => 10, 'bold' => true]);
        $phpWord->addTitleStyle(3, ['size' => 8, 'bold' => true]); //stile per le famiglie

        $colStyle4Next = ['colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'nextColumn'];
        $colStyle4NCont = ['colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'continuous'];

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
        $section->addText('POPOLAZIONE DI NOMADELFIA', ['bold' => true, 'italic' => false, 'size' => 14],
            ['align' => TextAlignment::CENTER]);
        $section->addTextBreak(2);
        $section->addText('Totale ' . $data->totalePopolazione,
            ['bold' => true, 'italic' => false, 'size' => 12],
            ['align' => TextAlignment::CENTER]);

        // Add TOC #1
        $section = $phpWord->addSection();
        $section->addTitle('Indice', 0);
        $section->addTextBreak(2);
        $toc = $section->addTOC($fontStyle12);
        $toc->setMaxDepth(2);
        $section->addTextBreak(2);

        // Section maggiorenni
        if ($elenchi->contains('maggMin')) {
            $section->addPageBreak();
            $maggiorenni = $data->maggiorenni;
            $section = $phpWord->addSection();
            $section->addTitle('Maggiorenni ' . $maggiorenni->total, 1);

            $sectMaggUomini = $phpWord->addSection($colStyle4NCont);
            $sectMaggUomini->addTitle('Uomini ' . count($maggiorenni->uomini), 2);
            foreach ($maggiorenni->uomini as $value) {
                $sectMaggUomini->addText($value->nominativo);
            }
            $maggDonne = $phpWord->addSection($colStyle4Next);
            $maggDonne->addTitle('Donne ' . count($maggiorenni->donne), 2);
            foreach ($maggiorenni->donne as $value) {
                $maggDonne->addText($value->nominativo);
            }
            // Figli minorenni
            $minorenni = $data->figliMinorenni;
            $section = $phpWord->addSection()->addTitle('Figli Minorenni ' . $minorenni->total, 1);
            $sectMinorenniUomini = $phpWord->addSection($colStyle4NCont);
            $sectMinorenniUomini->addTitle('Uomini ' . count($minorenni->uomini), 2);
            foreach ($minorenni->uomini as $value) {
                $sectMinorenniUomini->addText($value->nominativo);
            }
            $maggDonne = $phpWord->addSection($colStyle4Next);
            $maggDonne->addTitle('Donne ' . count($minorenni->donne), 2);
            foreach ($minorenni->donne as $value) {
                $maggDonne->addText($value->nominativo);
            }
        }
        if ($elenchi->contains('effePostOspFig')) {
            // Effettivi
            $effettivi = $data->effettivi;
            $section = $phpWord->addSection()->addTitle('Effettivi ' . $effettivi->total, 1);

            $effeUomini = $phpWord->addSection($colStyle4NCont);
            $effeUomini->addTitle('Uomini ' . count($effettivi->uomini), 2);
            foreach ($effettivi->uomini as $value) {
                $effeUomini->addText($value->nominativo);
            }
            $effeDonne = $phpWord->addSection($colStyle4Next);
            $effeDonne->addTitle('Donne ' . count($effettivi->donne), 2);
            foreach ($effettivi->donne as $value) {
                $effeDonne->addText($value->nominativo);
            }

            // Postulanti
            $postulanti = $data->postulanti;
            $postSect = $phpWord->addSection($colStyle4Next);
            $postSect->addTitle('Postulanti ' . $postulanti->total, 1);

            $postSect->addTitle('Uomini ' . count($postulanti->uomini), 2);
            foreach ($postulanti->uomini as $value) {
                $postSect->addText($value->nominativo);
            }
            $postSect->addTitle('Donne ' . count($postulanti->donne), 2);
            foreach ($postulanti->donne as $value) {
                $postSect->addText($value->nominativo);
            }

            // Ospiti
            $ospiti = $data->ospiti;
            $postSect = $phpWord->addSection($colStyle4Next);
            $postSect->addTitle('Ospiti ' . $ospiti->total, 1);
            $postSect->addTitle('Uomini ' . count($ospiti->uomini), 2);
            foreach ($ospiti->uomini as $value) {
                $postSect->addText($value->nominativo);
            }
            $postSect->addTitle('Donne ' . count($ospiti->donne), 2);
            foreach ($ospiti->donne as $value) {
                $postSect->addText($value->nominativo);
            }

            // Sacerdoti
            $sacerdoti = $data->sacerdoti;
            $sacSect = $phpWord->addSection($colStyle4Next);
            $sacSect->addTitle('Sacerdoti ' . count($sacerdoti), 2);
            foreach ($sacerdoti as $value) {
                $sacSect->addText($value->nominativo);
            }

            // Mamme di vocazione
            $mvocazione = $data->mammeVocazione;
            $mvocSect = $phpWord->addSection($colStyle4Next);
            $mvocSect->addTitle('Mamme Di Vocazione ' . count($mvocazione), 2);
            foreach ($mvocazione as $value) {
                $mvocSect->addText($value->nominativo);
            }

            // Figli >21
            $figliMag21 = $data->figliMaggiori21;
            $figlMagSect = $phpWord->addSection($colStyle4Next);
            $figlMagSect->addTitle('Figli/e >21 ' . $figliMag21->total, 1);
            $figlMagSect->addTitle('Figli ' . count($figliMag21->uomini), 2);
            foreach ($figliMag21->uomini as $value) {
                $figlMagSect->addText($value->nominativo);
            }
            $figlMagSect->addTitle('Figlie ' . count($figliMag21->donne), 2);
            foreach ($figliMag21->donne as $value) {
                $figlMagSect->addText($value->nominativo);
            }

            // Figli 18-21
            $figli21 = $data->figliFra18e21;
            $figliSect = $phpWord->addSection($colStyle4Next);
            $figliSect->addTitle('Figli/e 18...21 ' . $figli21->total, 1);
            $figliSect->addTitle('Figli ' . count($figli21->uomini), 2);
            foreach ($figli21->uomini as $value) {
                $figliSect->addText($value->nominativo);
            }
            $figliSect->addTitle('Figlie ' . count($figli21->donne), 2);
            foreach ($figli21->donne as $value) {
                $figliSect->addText($value->nominativo);
            }
        }
        if ($elenchi->contains('famiglie')) {
            // Famiglie
            $famiglieSect = $phpWord->addSection($colStyle4NCont);
            $famiglieSect->addPageBreak();
            $famiglieSect->addTitle('Famiglie ' . count($data->famiglie), 1);
            foreach ($data->famiglie as $id => $componenti) {
                $famiglieSect->addTextBreak(1);
                foreach ($componenti as $componente) {
                    if (!Str::startsWith($componente->posizione_famiglia, 'FIGLIO')) {
                        $famiglieSect->addTitle($componente->nominativo, 3);
                    } else {
                        $year = Carbon::parse($componente->data_nascita)->year;
                        $famiglieSect->addText('    ' . $year . ' ' . $componente->nominativo);
                    }
                }
            }
        }
        if ($elenchi->contains('gruppi')) {
            // gruppi familiari
            // $gruppiSect = $phpWord->addSection();
            // $gruppiSect->addTitle('Gruppi Familiari ', 1);

            foreach ($data->gruppiFamiliari as $gruppo) {
                $gruppiSect = $phpWord->addSection($colStyle4Next);
                $gruppiSect->addTitle($gruppo->nome . ' ' . $gruppo->personeAttuale()->count(), 2);

                foreach (GruppoFamiliare::single($gruppo)->get() as $single) {
                    $gruppiSect->addTitle($single->nominativo, 3);
                }
                foreach (collect(GruppoFamiliare::families($gruppo)->get())->groupBy('famiglia_id') as $famiglia_id => $componenti) {
                    $gruppiSect->addTextBreak(1);
                    foreach ($componenti as $componente) {
                        if (!Str::startsWith($componente->posizione_famiglia, 'FIGLIO')) {
                            $gruppiSect->addText($componente->nominativo, ['bold' => true]);
                        } else {
                            $year = Carbon::parse($componente->data_nascita)->year;
                            $gruppiSect->addText('    ' . $year . ' ' . $componente->nominativo);
                        }
                    }
                }
            }
        }
        if ($elenchi->contains('aziende')) {
            // Aziende
            $azi = $phpWord->addSection();
            $azi->addTitle('Aziende ', 1);
            $sectAziende = $phpWord->addSection($colStyle4NCont);
            foreach ($data->aziende as $azienda) {
                $sectAziende->addTextBreak(1);
                $lavoratori = $azienda->lavoratoriAttuali()->get();
                $sectAziende->addTitle($azienda->nome_azienda . '  ' . count($lavoratori), 3);
                foreach ($lavoratori as $lavoratore) {
                    $sectAziende->addText('    ' . $lavoratore->nominativo);
                }
            }
        }
        if ($elenchi->contains('incarichi')) {
            // Incarichi
            $azi = $phpWord->addSection();

            $azi->addTitle('Incarichi ' . $data->incarichi->count(), 1);
            $sectAziende = $phpWord->addSection($colStyle4NCont);
            foreach ($data->incarichi as $incarico) {
                $sectAziende->addTextBreak(1);
                $lavoratori = $incarico->lavoratoriAttuali()->get();
                $sectAziende->addTitle($incarico->nome . '  ' . count($lavoratori), 3);
                foreach ($lavoratori as $lavoratore) {
                    $sectAziende->addText('    ' . $lavoratore->nominativo);
                }
            }
        }
        if ($elenchi->contains('scuola')) {
            $sc = $phpWord->addSection();
            $sc->addTitle('Scuola ' . $data->annoScolasticoAlunni, 1);

            $classeSect = $phpWord->addSection($colStyle4NCont);
            foreach ($data->classi as $classe) {
                $alunni = $classe->alunni();
                if ($alunni->count() > 0) {
                    $classeSect->addTextBreak(1);
                    $classeSect->addTitle($classe->tipo->nome . ' ' . $alunni->count(), 2);
                    foreach ($classe->alunni()->get() as $alunno) {
                        $year = Carbon::parse($alunno->data_nascita)->year;
                        $classeSect->addText('    ' . $year . ' ' . $alunno->nominativo);
                    }
                }
            }

        }

        return $phpWord;
    }
}
