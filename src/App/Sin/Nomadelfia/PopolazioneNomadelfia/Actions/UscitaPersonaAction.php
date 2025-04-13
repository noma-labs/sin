<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use App\Nomadelfia\Persona\Models\Persona;

final readonly class UscitaPersonaAction
{
    public function __construct(
        private UscitaPersonaDBAction $uscita,
        private LogUscitaPersonaAction $logUscitaActivity
    ) {}

    /*
    * Fa uscire una persona da Nomadelfia aggiornando tutte le posizioni attuali con la data di uscita.
    * Se disable_from_family=True e se è un figlio, la persona viene anche messa fuori dal nucleo familiare.
    */
    public function execute(Persona $persona, Carbon $data_uscita, bool $disableFromFamily = false): void
    {
        $this->uscita->execute($persona, $data_uscita, $disableFromFamily);

        $data_entrata = $persona->getDataEntrataNomadelfia();

        $this->logUscitaActivity->execute(
            $persona,
            $data_entrata,
            $data_uscita,
        );
    }
}
