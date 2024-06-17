<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;

class UscitaPersonaAction
{
    private LogUscitaPersonaAction $logUscitaActivity;

    private UscitaPersonaDBAction $uscita;

    private SendEmailPersonaUscitaAction $email;

    public function __construct(
        UscitaPersonaDBAction $uscita,
        LogUscitaPersonaAction $logUscitaActivity,
        SendEmailPersonaUscitaAction $email
    ) {
        $this->uscita = $uscita;
        $this->logUscitaActivity = $logUscitaActivity;
        $this->email = $email;
    }

    /*
    * Fa uscire una persona da Nomadelfia aggiornando tutte le posizioni attuali con la data di uscita.
    * Se disable_from_family=True e se è un figlio, la persona viene anche messa fuori dal nucleo familiare.
    */
    public function execute(Persona $persona, string $data_uscita, bool $disableFromFamily = false): void
    {
        $this->uscita->execute($persona, $data_uscita, $disableFromFamily);

        $data_entrata = $this->calcDataEntrata($persona);

        $this->logUscitaActivity->execute(
            $persona,
            $data_entrata,
            $data_uscita,
        );

        $this->email->execute(
            $persona,
            $data_entrata,
            $data_uscita
        );
    }

    public function calcDataEntrata(Persona $persona)
    {
        // TODO: if there is not data entrata raise an exception because it is not possible that a person is leaving the
        // community without having a entering date.
        return $persona->getDataEntrataNomadelfia() ?: '';
    }
}
