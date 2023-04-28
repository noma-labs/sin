<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonExitedMail;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

class SendEmailPersonaUscitaAction
{
    public function __construct()
    {
    }

    public function execute(Persona $persona, string $data_entrata, string $data_uscita)
    {
        Mail::to('davideneri18@gmail.com')
            ->send(new PersonExitedMail($persona, $data_entrata, $data_uscita));
    }
}
