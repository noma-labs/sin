<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;

class ExportAnnoScolastico
{
    public int $totaleAlunni;

    public string $responsabile;

    public $prescuola;

    public $elementari;

    public $medie;

    public $superiori;

    public function __construct(Anno $anno)
    {
        $this->responsabile = is_null($anno->responsabile) ? 'non asseganto' : $anno->responsabile->nominativo;
        $this->totaleAlunni = Studente::InAnnoScolastico($anno)->count();
        $this->prescuola = $anno->prescuola()->alunni('data_nascita', 'DESC')->get();
        $this->elementari = $anno->prescuola()->alunni('data_nascita', 'DESC')->get();
    }
}
