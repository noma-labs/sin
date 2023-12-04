<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Carbon;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Cariche;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class CaricheController extends CoreBaseController
{
    /**
     * view della pagina index per la gestion delle cariche costituzionali
     *
     * @author Davide Neri
     **/
    public function index()
    {
        $ass = Cariche::AssociazioneCariche();
        $sol = Cariche::SolidarietaCariche();
        $fon = Cariche::FondazioneCariche();
        $agr = Cariche::AgricolaCariche();
        $cul = Cariche::CulturaleCariche();

        return view('nomadelfia.cariche.index', compact('ass', 'sol', 'fon', 'agr', 'cul'));
    }

    /**
     * view della pagina di visualizazione delle persone eleggibili per ogni carica
     *
     * @author Davide Neri
     **/
    public function elezioni()
    {
        $anz = Cariche::EleggibiliConsiglioAnziani();

        return view('nomadelfia.cariche.elezioni', compact('anz'));
    }

    public function esporta()
    {
        $anz = Cariche::EleggibiliConsiglioAnziani();

        $phpWord = new PhpWord();
        // define styles
        $fontStyle12 = ['size' => 10, 'spaceAfter' => 60];
        $phpWord->addTitleStyle(1, ['size' => 12, 'bold' => true, 'allCaps' => true], ['spaceAfter' => 240]);
        $phpWord->addTitleStyle(2, ['size' => 10, 'bold' => true]);
        $phpWord->addTitleStyle(3, ['size' => 8, 'bold' => true]); //stile per le famiglie
        $colStyle4Next = ['colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'nextColumn'];
        $colStyle4NCont = ['colsNum' => 4, 'colsSpace' => 300, 'breakType' => 'continuous'];

        // Consiglio degli anziani eleggibili
        $section = $phpWord->addSection();
        $section->addTitle('Consiglio degli anziani '.$anz->total, 1);

        $sectAnzUomini = $phpWord->addSection($colStyle4NCont);
        $sectAnzUomini->addTitle('Uomini '.count($anz->uomini), 2);
        foreach ($anz->uomini as $value) {
            $sectAnzUomini->addText($value->nominativo);
        }
        $maggDonne = $phpWord->addSection($colStyle4Next);
        $maggDonne->addTitle('Donne '.count($anz->donne), 2);
        foreach ($anz->donne as $value) {
            $maggDonne->addText($value->nominativo);
        }
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "elezioni-$data.docx";
        $objWriter->save(storage_path($file_name));

        return response()->download(storage_path($file_name));
    }
}
