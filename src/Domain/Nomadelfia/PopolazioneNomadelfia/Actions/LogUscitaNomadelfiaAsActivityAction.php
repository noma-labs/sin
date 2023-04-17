<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaPersonaData;

class LogUscitaNomadelfiaAsActivityAction
{
    public function execute(UscitaPersonaData $uscitaData)
    {
        activity('nomadelfia.popolazione')
            ->performedOn($uscitaData->persona)
            ->withProperties([
                'nominativo' => $uscitaData->persona->nominativo,
                'data_nascita' => $uscitaData->persona->data_nascita,
                'data_entrata' => $uscitaData->data_entrata,
                'data_uscita' => $uscitaData->data_uscita,
                'numero_elenco' => $uscitaData->persona->numero_elenco,
            ]
            )
            ->setEvent('uscita')
            ->log(':properties.nominativo è uscito/a da Nomadelfia in data :properties.data_uscita');

    }
}
