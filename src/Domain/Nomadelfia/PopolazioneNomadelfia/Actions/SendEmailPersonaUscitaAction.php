<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonExitedMail;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendEmailPersonaUscitaAction
{

    public function execute(Persona $persona, string $data_entrata, string $data_uscita)
    {
        $emails = ['davideneri18@gmail.com'];
        Mail::to($emails)
            ->send(new PersonExitedMail($persona, $data_entrata, $data_uscita));
    }
}
