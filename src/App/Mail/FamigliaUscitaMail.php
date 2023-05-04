<?php

namespace App\Mail;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class FamigliaUscitaMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Famiglia   $famiglia,
        public Collection $componenti,
        public string     $data_uscita,
    )
    {

    }

    public function build(): FamigliaUscitaMail
    {

        return $this
            ->subject('[Aggiornamento Anagrafe] Uscita famiglia')
            ->view('nomadelfia.mails.famigliaUscita');
    }


}
