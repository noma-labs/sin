<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonaDecessoMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

class SendEmailPersonaDecessoAction
{
    public function execute(Persona $persona, string $data_decesso)
    {
        if (is_string($data_decesso)) {
            $data_decesso = Carbon::parse($data_decesso);
        }
        $to = config('aggiornamento-anagrafe.to');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->send(new PersonaDecessoMail($persona, $data_decesso));
        }
    }
}
