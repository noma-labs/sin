<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaPersonaData;

class LogUscitaNomadelfiaAsActivityAction
{
    public function execute(Persona $persona, string $data_entrata, string $data_uscita)
    {
        activity('nomadelfia')
            ->performedOn($persona)
            ->withProperties([
                    'nominativo' => $persona->nominativo,
                    'luogo_nascita' => $persona->provincia_nascita,
                    'data_nascita' => $persona->data_nascita,
                    'data_entrata' => $data_entrata,
                    'data_uscita' => $data_uscita,
                    'numero_elenco' => $persona->numero_elenco,
                ]
            )
            ->setEvent('popolazione.uscita')
            ->log('Uscito da Nomadelfia in data :properties.data_uscita');

    }

}
