<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

final readonly class UscitaFamigliaAction
{
    public function __construct(
        private LogUscitaFamigliaAction $logUscita,
    ) {}

    public function execute(Famiglia $famiglia, Carbon $data_uscita): void
    {
        $dto = new UscitaFamigliaData;
        $dto->famiglia = $famiglia;
        $dto->componenti = $famiglia->componentiAttuali()->get();
        $dto->data_uscita = $data_uscita;

        $this->save($dto);
        $this->logUscita->execute($dto);
    }

    public function save(UscitaFamigliaData $dto): void
    {

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $uscita = $dto->data_uscita;
            $dto->componenti->each(function ($persona) use ($uscita): void {
                $act = resolve(UscitaPersonaDBAction::class);
                $act->execute($persona, $uscita);
            });
            DB::connection('db_nomadelfia')->commit();
        } catch (Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }

    }
}
