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

class PersonEnteredMail extends Mailable
{
    use Queueable, SerializesModels;

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
            ->subject("[Popolazione Nomadelfia] Nuova entrata")
            ->view("mails.personaEntrata");
        //Persona {$this->persona->nome} {$this->persona->cognome} Data Entrata: {$this->data_entrata}");
    }
    
}
