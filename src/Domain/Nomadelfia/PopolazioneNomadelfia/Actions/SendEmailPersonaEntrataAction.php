<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonaEntrataMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

final class SendEmailPersonaEntrataAction
{
    public function execute(Persona $persona, Carbon $data_entrata, GruppoFamiliare $gruppo, ?Famiglia $famiglia): void
    {
        $to = config('aggiornamento-anagrafe.to');
        $cc = config('aggiornamento-anagrafe.cc');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->cc($cc)->send(new PersonaEntrataMail($persona, $data_entrata, $gruppo, $famiglia));
        }
    }
}
