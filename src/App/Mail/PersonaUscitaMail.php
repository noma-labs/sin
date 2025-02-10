<?php

declare(strict_types=1);

namespace App\Mail;

use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class PersonaUscitaMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Persona $persona,
        public Carbon $data_entrata,
        public Carbon $data_uscita,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('[Aggiornamento Anagrafe] Uscita persona')
            ->markdown('nomadelfia.mails.personaUscita');
    }
}
