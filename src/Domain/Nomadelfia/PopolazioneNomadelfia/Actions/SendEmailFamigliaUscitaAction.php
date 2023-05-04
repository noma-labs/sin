<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\FamigliaUscitaMail;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;
use Illuminate\Support\Facades\Mail;

class SendEmailFamigliaUscitaAction
{
    public function execute(UscitaFamigliaData $dto)
    {
        $to = config('aggiornamento-anagrafe.to');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->send(new FamigliaUscitaMail($dto->famiglia, $dto->componenti, $dto->data_uscita));
        }
    }
}
