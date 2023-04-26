<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\PersonEnteredMail;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Mail;

class SendEmailEntrataAction
{
    public function __construct()
    {
    }

    public function execute(Persona $persona, string $data_entrata, GruppoFamiliare $gruppo, Famiglia|null $famiglia)
    {
        Mail::to('davideneri18@gmail.com')
            ->send(new PersonEnteredMail($persona, $data_entrata, $gruppo, $famiglia));
    }
}
