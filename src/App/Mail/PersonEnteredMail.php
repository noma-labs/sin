<?php

namespace App\Mail;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonEnteredMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Persona         $persona,
        public string          $data_entrata,
        public GruppoFamiliare $gruppo,
        public Famiglia|null   $famiglia
    )
    {

    }

    public function build(): PersonEnteredMail
    {
        return $this
            ->subject("[Aggiornamento Anagrafe] Persona entrata")
            ->view("nomadelfia.mails.personaEntrata");
    }

}
