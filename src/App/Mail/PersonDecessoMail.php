<?php

namespace App\Mail;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonDecessoMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Persona $persona,
        public string $data_decesso,
    ) {

    }

    public function build(): PersonDecessoMail
    {
        return $this
            ->subject('[Aggiornamento Anagrafe] Decesso persona')
            ->view('nomadelfia.mails.personaDeceduta');
    }
}
