<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\FamigliaUscitaMail;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;
use Illuminate\Support\Facades\Mail;

class SendEmailFamigliaUscitaAction
{
    public function execute(UscitaFamigliaData $dto)
    {
        Mail::to('davideneri18@gmail.com')
            ->send(new FamigliaUscitaMail($dto->famiglia, $dto->componenti, $dto->data_uscita));
    }
}