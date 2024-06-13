<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use Carbon;
use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\ExportPopolazioneToWordAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

class PopolazioneNomadelfiaController
{
    public function show()
    {
        return view('nomadelfia.popolazione.show');
    }

    public function maggiorenni(Request $request)
    {
        $maggiorenni = PopolazioneNomadelfia::maggiorenni('nominativo');
        // TODO: togliere da qui. messo solo per urgenza di creare es spirituali
        $esercizi = EserciziSpirituali::attivi()->get();

        return view('nomadelfia.popolazione.maggiorenni', ['maggiorenni' => $maggiorenni, 'esercizi' => $esercizi]);
    }

    public function effettivi(Request $request)
    {
        $effettivi = PopolazioneNomadelfia::effettivi();

        return view('nomadelfia.popolazione.effettivi', ['effettivi' => $effettivi]);
    }

    public function postulanti(Request $request)
    {
        $postulanti = PopolazioneNomadelfia::postulanti();

        return view('nomadelfia.popolazione.postulanti', ['postulanti' => $postulanti]);
    }

    public function ospiti(Request $request)
    {
        $ospiti = PopolazioneNomadelfia::ospiti();

        return view('nomadelfia.popolazione.ospiti', ['ospiti' => $ospiti]);
    }

    public function sacerdoti(Request $request)
    {
        $sacerdoti = PopolazioneNomadelfia::sacerdoti();

        return view('nomadelfia.popolazione.sacerdoti', ['sacerdoti' => $sacerdoti]);
    }

    public function mammeVocazione(Request $request)
    {
        $mvocazione = PopolazioneNomadelfia::mammeVocazione();

        return view('nomadelfia.popolazione.mammevocazione', ['mvocazione' => $mvocazione]);
    }

    public function nomadelfaMamma(Request $request)
    {
        $nmamma = PopolazioneNomadelfia::nomadelfaMamma();

        return view('nomadelfia.popolazione.nomadelfamamma', ['nmamma' => $nmamma]);
    }

    public function figliMaggiorenni(Request $request)
    {
        $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni();

        return view('nomadelfia.popolazione.figlimaggiorenni', ['maggiorenni' => $maggiorenni]);
    }

    public function figliMinorenni(Request $request)
    {
        $minorenni = PopolazioneNomadelfia::figliMinorenni();

        return view('nomadelfia.popolazione.figliminorenni', ['minorenni' => $minorenni]);
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
