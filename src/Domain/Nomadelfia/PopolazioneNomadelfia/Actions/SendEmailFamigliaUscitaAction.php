<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\FamigliaUscitaMail;
use Carbon\Carbon;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;
use Illuminate\Support\Facades\Mail;

class SendEmailFamigliaUscitaAction
{
    public function execute(UscitaFamigliaData $dto)
    {
        $data_uscita = Carbon::parse($dto->data_uscita);
        $to = config('aggiornamento-anagrafe.to');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->send(new FamigliaUscitaMail($dto->famiglia, $dto->componenti, $data_uscita));
        }
    }
}
