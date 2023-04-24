<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;

class LogEntrataInNomadelfiaActivityAction
{
    public function execute(EntrataPersonaData $entrataPersonaData)
    {
        activity('nomadelfia')
            ->performedOn($entrataPersonaData->persona)
            ->withProperties([
                'nominativo' => $entrataPersonaData->persona->nominativo,
                'data_entrata' => $entrataPersonaData->data_entrata,
                'data_nascita' => $entrataPersonaData->persona->data_nascita,
                'luogo_nascita' => $entrataPersonaData->persona->provincia_nascita,
                'gruppo' => $entrataPersonaData->gruppoFamiliare->nome,
                'numero_elenco' => $entrataPersonaData->persona->numero_elenco,
                'famiglia' => ($entrataPersonaData->famiglia) ? $entrataPersonaData->famiglia->nome_famiglia : null,
            ]
            )
            ->setEvent('popolazione.entrata')
            ->log('Nuova entrata in Nomadelfia in data :properties.data_entrata');

    }
}
