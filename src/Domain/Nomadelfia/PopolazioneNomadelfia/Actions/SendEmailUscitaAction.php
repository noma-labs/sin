<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonEnteredMail;
use App\Mail\PersonExitedMail;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use Illuminate\Support\Facades\Mail;

class SendEmailUscitaAction
{

    public function __construct()
    {
    }

    public function execute(Persona $persona, string $data_entrata, string $data_uscita)
    {
        Mail::to('davideneri18@gmail.com')
            ->send(new PersonExitedMail($persona, $data_entrata, $data_uscita));
    }

}
