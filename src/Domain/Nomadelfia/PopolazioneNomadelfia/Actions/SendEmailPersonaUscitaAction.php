<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonaUscitaMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

final class SendEmailPersonaUscitaAction
{
    public function execute(Persona $persona, Carbon $data_entrata, Carbon $data_uscita): void
    {
        $to = config('aggiornamento-anagrafe.to');
        $cc = config('aggiornamento-anagrafe.cc');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->cc($cc)->send(new PersonaUscitaMail($persona, $data_entrata, $data_uscita));
        }
    }
}
