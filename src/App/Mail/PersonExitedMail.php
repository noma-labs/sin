<?php

namespace App\Mail;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PersonExitedMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Persona $persona,
        public string  $data_entrata,
        public string  $data_uscita,
    )
    {

    }

    public function build(): PersonExitedMail
    {
        return $this
            ->subject("[Popolazione Nomadelfia] Nuova uscita")
            ->view("nomadelfia.mails.personaUscita");
    }

}
