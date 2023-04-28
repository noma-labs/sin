<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\FamigliaUscitaMail;
use App\Mail\PersonEnteredMail;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
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
