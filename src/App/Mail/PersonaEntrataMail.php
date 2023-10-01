<?php

namespace App\Mail;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonaEntrataMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Persona $persona,
        public Carbon $data_entrata,
        public GruppoFamiliare $gruppo,
        public ?Famiglia $famiglia
    ) {

    }

    public function build(): PersonaEntrataMail
    {
        return $this
            ->subject('[Aggiornamento Anagrafe] Entrata persona')
            ->markdown('nomadelfia.mails.personaEntrata');
    }
}
