<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;

class ExportPopolazioneData
{
    public int $totalePopolazione;

    public $maggiorenni;

    public $figliMinorenni;

    public $effettivi;

    public $postulanti;

    public $ospiti;

    public $sacerdoti;

    public $mammeVocazione;

    public $figliMaggiori21;

    public $figliFra18e21;

    public $famiglie;

    public $incarichi;

    public $gruppiFamiliari;

    public $aziende;

    public $annoScolasticoAlunni;

    public $scuola;

    public $classi;

    public function __construct()
    {
        $this->totalePopolazione = PopolazioneNomadelfia::totalePopolazione();
        $this->maggiorenni = PopolazioneNomadelfia::maggiorenni();
        $this->figliMinorenni = PopolazioneNomadelfia::figliMinorenni();
        $this->effettivi = PopolazioneNomadelfia::effettivi();
        $this->postulanti = PopolazioneNomadelfia::postulanti();
        $this->ospiti = PopolazioneNomadelfia::ospiti();
        $this->sacerdoti = PopolazioneNomadelfia::sacerdoti();
        $this->mammeVocazione = PopolazioneNomadelfia::mammeVocazione();
        $this->figliMaggiori21 = PopolazioneNomadelfia::figliMaggiori21();
        $this->figliFra18e21 = PopolazioneNomadelfia::figliFra18e21();
        $this->famiglie = PopolazioneNomadelfia::famiglie();

        $this->gruppiFamiliari = GruppoFamiliare::orderby('nome')->get();
        $this->aziende = Azienda::aziende()->get();
        $this->incarichi = Incarico::all();
        $anno = Anno::getLastAnno();
        $this->annoScolasticoAlunni = Studente::InAnnoScolastico($anno)->count();
        $this->classi = $anno->classi()->with('tipo')->get()->sortBy('tipo.ord');

    }

    public function getFileName(): string
    {
        $data = Carbon::now()->toDatestring();

        return "popolazione-$data.docx";
    }
}
