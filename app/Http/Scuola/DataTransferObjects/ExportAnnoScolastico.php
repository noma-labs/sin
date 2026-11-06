<?php

declare(strict_types=1);

namespace App\Scuola\DataTransferObjects;

use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use App\View\Components\PersonaNome;

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
        $this->responsabile = is_null($resp) ? 'non asseganto' : PersonaNome::testo($resp);
        $this->totaleAlunni = Studente::InAnnoScolastico($anno)->count();
        $this->prescuola = $anno->prescuola()->alunni('data_nascita', 'DESC')->get();
        $this->elementari = $anno->prescuola()->alunni('data_nascita', 'DESC')->get();
    }
}
