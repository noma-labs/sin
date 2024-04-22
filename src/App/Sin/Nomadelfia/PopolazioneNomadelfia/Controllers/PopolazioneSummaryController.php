<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;

class PopolazioneSummaryController
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

        $figli = PopolazioneNomadelfia::byPosizione('FIGL');

        $gruppi = GruppoFamiliare::countComponenti();
        $posizioniFamiglia = PopolazioneNomadelfia::posizioneFamigliaCount();
        $famiglieNumerose = Famiglia::famiglieNumerose();

        $activities = AggiornamentoAnagrafe::orderBy('created_at', 'DESC')->take(20)->get();

        return view('nomadelfia.summary', compact('totale', 'maggiorenni', 'effettivi', 'postulanti', 'ospiti', 'sacerdoti', 'mvocazione', 'nomanamma', 'figliMaggiorenni', 'minorenni', 'figli', 'gruppi', 'posizioniFamiglia', 'famiglieNumerose', 'stats', 'activities'));
    }
}
