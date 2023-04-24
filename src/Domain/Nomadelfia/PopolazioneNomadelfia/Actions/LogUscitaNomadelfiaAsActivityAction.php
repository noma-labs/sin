<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaPersonaData;

class LogUscitaNomadelfiaAsActivityAction
{
    public function execute(UscitaPersonaData $uscitaData)
    {
        activity('nomadelfia')
            ->performedOn($uscitaData->persona)
            ->withProperties([
                    'nominativo' => $uscitaData->persona->nominativo,
                    'luogo_nascita' => $uscitaData->persona->provincia_nascita,
                    'data_nascita' => $uscitaData->persona->data_nascita,
                    'data_entrata' => $uscitaData->data_entrata,
                    'data_uscita' => $uscitaData->data_uscita,
                    'numero_elenco' => $uscitaData->persona->numero_elenco,
                ]
            )
            ->setEvent('popolazione.uscita')
            ->log('Uscito da Nomadelfia in data :properties.data_uscita');

    }
}
