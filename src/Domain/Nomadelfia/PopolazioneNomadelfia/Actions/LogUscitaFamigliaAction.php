<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaPersonaData;

class LogUscitaFamigliaAction
{
    public function execute(UscitaFamigliaData $dto)
    {
        LogBatch::startBatch();
//        activity('nomadelfia')
//            ->performedOn($dto->famiglia)
//            ->withProperties([
//                    'nominativo' => $persona->nominativo,
//                    'luogo_nascita' => $persona->provincia_nascita,
//                    'data_nascita' => $persona->data_nascita,
//                    'data_entrata' => $data_entrata,
//                    'data_uscita' => $data_uscita,
//                    'numero_elenco' => $persona->numero_elenco,
//                ]
//            )
//            ->setEvent('popolazione.uscita-famiglia')
//            ->log('Nuova famiglia uscita da Nomadelfia in data :properties.data_uscita');

        activity('nomadelfia')
            ->performedOn($persona)
            ->withProperties([
                    'nominativo' => $persona->nominativo,
                    'data_entrata' => $data_entrata,
                    'data_nascita' => $persona->data_nascita,
                    'luogo_nascita' => $persona->provincia_nascita,
                    'gruppo' => $gruppo->nome,
                    'numero_elenco' => $persona->numero_elenco,
                    'famiglia' => ($famiglia) ? $famiglia->nome_famiglia : null
                ]
            )
            ->setEvent('popolazione.entrata')
            ->log('Nuova entrata in Nomadelfia in data :properties.data_entrata');

        LogBatch::endBatch();

    }

}
