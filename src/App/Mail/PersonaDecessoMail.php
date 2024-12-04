<?php

declare(strict_types=1);

namespace App\Mail;

use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class PersonaDecessoMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Persona $persona,
        public Carbon $data_decesso,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('[Aggiornamento Anagrafe] Decesso persona')
            ->markdown('nomadelfia.mails.personaDeceduta');
    }
}
