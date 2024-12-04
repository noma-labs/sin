<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;
use Exception;
use Illuminate\Support\Facades\DB;

final class UscitaFamigliaAction
{
    public function __construct(
        private LogUscitaFamigliaAction $logUscita,
        private SendEmailFamigliaUscitaAction $emailUscita
    ) {}

    public function execute(Famiglia $famiglia, Carbon $data_uscita): void
    {
        $dto = new UscitaFamigliaData;
        $dto->famiglia = $famiglia;
        $dto->componenti = $famiglia->componentiAttuali()->get();
        $dto->data_uscita = $data_uscita;

        $this->save($dto);
        $this->logUscita->execute($dto);
        $this->emailUscita->execute($dto);
    }

    public function save(UscitaFamigliaData $dto): void
    {

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $uscita = $dto->data_uscita;
            $dto->componenti->each(function ($persona) use ($uscita): void {
                $act = app(UscitaPersonaDBAction::class);
                $act->execute($persona, $uscita);
            });
            DB::connection('db_nomadelfia')->commit();
        } catch (Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }

    }
}
