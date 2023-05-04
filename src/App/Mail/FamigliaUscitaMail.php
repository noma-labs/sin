<?php

namespace App\Mail;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FamigliaUscitaMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Famiglia $famiglia,
        public Collection $componenti,
        public Carbon $data_uscita,
    ) {

    }

    public function build(): FamigliaUscitaMail
    {

        return $this
            ->subject('[Aggiornamento Anagrafe] Uscita famiglia')
            ->markdown('nomadelfia.mails.famigliaUscita');
    }
}
