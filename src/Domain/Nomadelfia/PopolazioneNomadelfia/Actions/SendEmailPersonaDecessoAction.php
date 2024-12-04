<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonaDecessoMail;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

final class SendEmailPersonaDecessoAction
{
    public function execute(Persona $persona, string $data_decesso): void
    {
        $data_decesso = Carbon::parse($data_decesso);
        $to = config('aggiornamento-anagrafe.to');
        $cc = config('aggiornamento-anagrafe.cc');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->cc($cc)->send(new PersonaDecessoMail($persona, $data_decesso));
        }
    }
}
