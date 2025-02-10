<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;

final class ExportAnnoScolastico
{
    public int $totaleAlunni;

    public string $responsabile;

    public $prescuola;

    public $elementari;

    public $medie;

    public $superiori;

    public function __construct(Anno $anno)
    {
        $resp = $anno->responsabile()->first();
        $this->responsabile = is_null($resp) ? 'non asseganto' : $resp->nominativo;
        $this->totaleAlunni = Studente::InAnnoScolastico($anno)->count();
        $this->prescuola = $anno->prescuola()->alunni('data_nascita', 'DESC')->get();
        $this->elementari = $anno->prescuola()->alunni('data_nascita', 'DESC')->get();
    }
}
