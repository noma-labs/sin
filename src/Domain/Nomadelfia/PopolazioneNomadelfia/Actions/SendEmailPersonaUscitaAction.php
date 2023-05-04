<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonExitedMail;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

class SendEmailPersonaUscitaAction
{
    public function execute(Persona $persona, string $data_entrata, string $data_uscita)
    {
        $to = config('aggiornamento-anagrafe.to');
        if (config('aggiornamento-anagrafe.enabled'))
            Mail::to($to)->send(new PersonExitedMail($persona, $data_entrata, $data_uscita));
    }
}
