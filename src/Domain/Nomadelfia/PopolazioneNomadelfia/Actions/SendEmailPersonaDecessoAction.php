<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonDecessoMail;
use App\Mail\PersonExitedMail;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

class SendEmailPersonaDecessoAction
{

    public function execute(Persona $persona, string $data_decesso)
    {
        Mail::to('davideneri18@gmail.com')
            ->send(new PersonDecessoMail($persona, $data_decesso));
    }
}
