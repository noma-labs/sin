<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\ExportPopolazioneToWordAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\ExportPopolazioneData;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneAttuale;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Scuola\Models\Anno;
use App\Traits\SortableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
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
        $stats = PopolazioneNomadelfia::stats();


        $figli = PopolazioneNomadelfia::byPosizione("FIGL");

        $gruppi = GruppoFamiliare::countComponenti();
        $posizioniFamiglia = PopolazioneNomadelfia::posizioneFamigliaCount();
        $famiglieNumerose = Famiglia::famiglieNumerose();


        return view("nomadelfia.summary", compact('totale', 'maggiorenni', 'effettivi', 'postulanti', 'ospiti', 'sacerdoti', 'mvocazione', 'nomanamma', 'figliMaggiorenni', 'minorenni', 'figli', 'gruppi', 'posizioniFamiglia', 'famiglieNumerose', 'stats'));
    }

    public function show()
    {
        return view("nomadelfia.popolazione.show");
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
        $action = new ExportPopolazioneToWordAction();
        $word = $action->execute($elenchi);

        $objWriter = IOFactory::createWriter($word, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "popolazione-$data.docx";

        $objWriter->save(storage_path($file_name));
        return response()->download(storage_path($file_name));
    }

}