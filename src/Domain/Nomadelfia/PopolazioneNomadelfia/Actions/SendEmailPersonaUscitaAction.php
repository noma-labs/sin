<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonaUscitaMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

class SendEmailPersonaUscitaAction
{
    public function execute(Persona $persona, string $data_entrata, string $data_uscita)
    {
        if (is_string($data_uscita)) {
            $data_uscita = Carbon::parse($data_uscita);
        }
        if (is_string($data_entrata)) {
            $data_entrata = Carbon::parse($data_entrata);
        }
        $to = config('aggiornamento-anagrafe.to');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->send(new PersonaUscitaMail($persona, $data_entrata, $data_uscita));
        }
    }
}
