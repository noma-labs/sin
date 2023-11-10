<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;
use Illuminate\Support\Facades\DB;

class UscitaFamigliaAction
{
    private LogUscitaFamigliaAction $logUscita;

    private SendEmailFamigliaUscitaAction $emailUscita;

    public function __construct(
        LogUscitaFamigliaAction $logUscita,
        SendEmailFamigliaUscitaAction $emailUscita
    ) {
        $this->logUscita = $logUscita;
        $this->emailUscita = $emailUscita;
    }

    public function execute(Famiglia $famiglia, string $data_uscita)
    {
        $dto = new UscitaFamigliaData();
        $dto->famiglia = $famiglia;
        $dto->componenti = $famiglia->componentiAttuali()->get();
        $dto->data_uscita = $data_uscita;

        $this->save($dto);
        $this->logUscita->execute($dto);
        $this->emailUscita->execute($dto);
    }

    public function save(UscitaFamigliaData $dto)
    {

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $uscita = $dto->data_uscita;
            $dto->componenti->each(function ($persona) use ($uscita) {
                $act = app(UscitaPersonaDBAction::class);
                $act->execute($persona, $uscita);
            });
            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }

    }
}
