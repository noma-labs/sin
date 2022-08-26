<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Carbon\Carbon;
use Domain\Exports\ToWord;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;

class ExportPopolazioneData implements ToWord
{

    public int $totalePopolazione;

    public $maggiorenni;

    public $minorenni;

    public $effettivi;

    public $postulanti;

    public $ospiti;

    public $sacerdoti;

    public $mammeVocazione;

    public $figliMagg21;

    public $figliFra18e21;

    public $famiglie;

    public $gruppiFamiliari;

    public $aziende;

    public $scuola;

    function __construct()
    {
        $this->totalePopolazione = PopolazioneNomadelfia::totalePopolazione();
        $this->maggiorenni = PopolazioneNomadelfia::maggiorenni();
        $this->minorenni = PopolazioneNomadelfia::figliMinorenni();
        $this->effettivi = PopolazioneNomadelfia::effettivi();
        $this->postulanti = PopolazioneNomadelfia::postulanti();
    }


    function getFileName(): string
    {
        $data = Carbon::now()->toDatestring();
        return "popolazione-$data.docx";
    }
}