<?php

namespace App\Mail;

use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonaUscitaMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Persona $persona,
        public Carbon $data_entrata,
        public Carbon $data_uscita,
    ) {}

    public function build(): PersonaUscitaMail
    {
        return $this
            ->subject('[Aggiornamento Anagrafe] Uscita persona')
            ->markdown('nomadelfia.mails.personaUscita');
    }
}
