<?php

namespace App\Mail;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonExitedMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Persona $persona,
        public string $data_entrata,
        public string $data_uscita,
    ) {

    }

    public function build(): PersonExitedMail
    {
        return $this
            ->subject('[Aggiornamento Anagrafe] Persona uscita')
            ->view('nomadelfia.mails.personaUscita');
    }
}