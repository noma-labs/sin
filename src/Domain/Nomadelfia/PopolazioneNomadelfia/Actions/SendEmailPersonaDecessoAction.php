<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonDecessoMail;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

class SendEmailPersonaDecessoAction
{
    public function execute(Persona $persona, string $data_decesso)
    {
        $to = config('aggiornamento-anagrafe.to');
        if (config('aggiornamento-anagrafe.enabled'))
            Mail::to($to)->send(new PersonDecessoMail($persona, $data_decesso));
    }
}
