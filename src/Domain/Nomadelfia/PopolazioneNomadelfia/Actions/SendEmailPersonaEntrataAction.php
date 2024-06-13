<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonaEntrataMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

class SendEmailPersonaEntrataAction
{
    public function execute(Persona $persona, string $data_entrata, GruppoFamiliare $gruppo, ?Famiglia $famiglia)
    {
        $data_entrata = Carbon::parse($data_entrata);
        $to = config('aggiornamento-anagrafe.to');
        $cc = config('aggiornamento-anagrafe.cc');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->cc($cc)->send(new PersonaEntrataMail($persona, $data_entrata, $gruppo, $famiglia));
        }
    }
}
