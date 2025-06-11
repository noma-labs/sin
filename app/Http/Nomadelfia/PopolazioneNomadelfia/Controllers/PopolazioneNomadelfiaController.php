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

        $query = PopolazioneAttuale::query();

        if (! $ageFilter->isEmpty()) {
            $age = $ageFilter->toString();
            if ($age === 'overage') {
                $query = $query->overage();
            } elseif ($age === 'underage') {
                $query = $query->underage();
            }
        }
        if (! $positionFilter->isEmpty()) {
            $position = $positionFilter->toString();
            if ($position === 'effettivo') {
                $query = $query->effettivo();
            } elseif ($position === 'postulante') {
                $query = $query->postulante();
            } elseif ($position === 'ospite') {
                $query = $query->ospite();
            } elseif ($position === 'figlio') {
                $query = $query->figlio();
            }
        }
        if (! $sexFilter->isEmpty()) {
            $sex = $sexFilter->toString();
            if ($sex === 'male') {
                $query = $query->male();
            } elseif ($sex === 'female') {
                $query = $query->female();
            }
        }

        $population = $query->sortable()->get();

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
