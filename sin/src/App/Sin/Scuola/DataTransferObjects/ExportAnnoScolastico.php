<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use App\Nomadelfia\Azienda\Controllers\AziendeController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;

class ExportAnnoScolastico
{

    public int $totaleAlunni;

    public string $responsabile;

    public $prescuola;

    public $elementari;

    public $medie;

    public $superiori;


    function __construct(Anno $anno)
    {
        $this->responsabile = is_null($anno->responsabile) ? 'non asseganto' : $anno->responsabile->nominativo;
        $this->totaleAlunni = Studente::InAnnoScolastico($anno)->count();
        $this->prescuola = $anno->prescuola()->alunni('data_nascita', 'DESC')->get();
        $this->elementari = $anno->prescuola()->alunni('data_nascita', 'DESC')->get();
    }


}