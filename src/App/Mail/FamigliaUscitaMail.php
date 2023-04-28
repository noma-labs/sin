<?php

namespace App\Mail;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FamigliaUscitaMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Famiglia   $famiglia,
        public Collection $componenti,
        public string     $data_uscita,
    )
    {

    }

    public function build(): FamigliaUscitaMail
    {
        return $this
            ->subject("[Aggiornamento Anagrafe] Famiglia uscita")
            ->view("nomadelfia.mails.famigliaUscita");
    }

}
