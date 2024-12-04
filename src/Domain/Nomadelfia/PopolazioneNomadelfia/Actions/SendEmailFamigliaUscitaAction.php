<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Mail\FamigliaUscitaMail;
use Carbon\Carbon;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;
use Illuminate\Support\Facades\Mail;

final class SendEmailFamigliaUscitaAction
{
    public function execute(UscitaFamigliaData $dto): void
    {
        $to = config('aggiornamento-anagrafe.to');
        $cc = config('aggiornamento-anagrafe.cc');
        if (config('aggiornamento-anagrafe.enabled')) {
            Mail::to($to)->cc($cc)->send(new FamigliaUscitaMail($dto->famiglia, $dto->componenti, $dto->data_uscita));
        }
    }
}
