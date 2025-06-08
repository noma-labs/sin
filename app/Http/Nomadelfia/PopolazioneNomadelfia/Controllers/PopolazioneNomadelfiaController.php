<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneAttuale;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;

final class PopolazioneNomadelfiaController
{
    public function index(Request $request)
    {
        $ageFilter = $request->string('age');
        $positionFilter = $request->string('position');
        $sexFilter = $request->string('sex');

        $query = PopolazioneAttuale::sortable();

        if (! $ageFilter->isEmpty()) {
            match ($ageFilter->toString()) {
                'overage' => $query->overage(),
                'underage' => $query->underage(),
                default => null,
            };
        }
        if (! $positionFilter->isEmpty()) {
            match ($positionFilter->toString()) {
                'effettivo' => $query->effettivo(),
                'postulante' => $query->postulante(),
                'ospite' => $query->ospite(),
                'figlio' => $query->figlio(),
                default => null,
            };
        }
        if (! $sexFilter->isEmpty()) {
            match ($sexFilter->toString()) {
                'male' => $query->male(),
                'female' => $query->female(),
                default => null,
            };
        }

        $population = $query->get();

        return view('nomadelfia.popolazione.index', compact('population'));
    }

    public function maggiorenni()
    {
        $maggiorenni = PopolazioneNomadelfia::maggiorenni('nominativo');

        return view('nomadelfia.popolazione.maggiorenni', compact('maggiorenni'));
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
}
