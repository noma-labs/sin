<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use Carbon;
use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\ExportPopolazioneToWordAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

final class PopolazioneNomadelfiaController
{
    public function show()
    {
        return view('nomadelfia.popolazione.show');
    }

    public function maggiorenni()
    {
        $maggiorenni = PopolazioneNomadelfia::maggiorenni('nominativo');
        // TODO: togliere da qui. messo solo per urgenza di creare es spirituali
        $esercizi = EserciziSpirituali::attivi()->get();

        return view('nomadelfia.popolazione.maggiorenni', compact('maggiorenni', 'esercizi'));
    }

    public function effettivi()
    {
        $effettivi = PopolazioneNomadelfia::effettivi();

        return view('nomadelfia.popolazione.effettivi', compact('effettivi'));
    }

    public function postulanti()
    {
        $postulanti = PopolazioneNomadelfia::postulanti();

        return view('nomadelfia.popolazione.postulanti', compact('postulanti'));
    }

    public function ospiti()
    {
        $ospiti = PopolazioneNomadelfia::ospiti();

        return view('nomadelfia.popolazione.ospiti', compact('ospiti'));
    }

    public function sacerdoti()
    {
        $sacerdoti = PopolazioneNomadelfia::sacerdoti();

        return view('nomadelfia.popolazione.sacerdoti', compact('sacerdoti'));
    }

    public function mammeVocazione()
    {
        $mvocazione = PopolazioneNomadelfia::mammeVocazione();

        return view('nomadelfia.popolazione.mammevocazione', compact('mvocazione'));
    }

    public function nomadelfaMamma()
    {
        $nmamma = PopolazioneNomadelfia::nomadelfaMamma();

        return view('nomadelfia.popolazione.nomadelfamamma', compact('nmamma'));
    }

    public function figliMaggiorenni()
    {
        $maggiorenni = PopolazioneNomadelfia::figliMaggiorenni();

        return view('nomadelfia.popolazione.figlimaggiorenni', compact('maggiorenni'));
    }

    public function figliMinorenni()
    {
        $minorenni = PopolazioneNomadelfia::figliMinorenni();

        return view('nomadelfia.popolazione.figliminorenni', compact('minorenni'));
    }

    public function print(Request $request)
    {
        $elenchi = collect($request->elenchi);
        $action = new ExportPopolazioneToWordAction;
        $word = $action->execute($elenchi);

        $objWriter = IOFactory::createWriter($word, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "popolazione-$data.docx";

        $objWriter->save(storage_path($file_name));

        return response()->download(storage_path($file_name));
    }
}
