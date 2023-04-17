<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class LogEntrataInNomadelfiaActivityAction
{

    public function execute(EntrataPersonaData $entrataPersonaData)
    {
        activity('nomadelfia.popolazione')
            ->performedOn($entrataPersonaData->persona)
            ->withProperties([
                    'nominativo' => $entrataPersonaData->persona->nominativo,
                    'data_entrata' => $entrataPersonaData->data_entrata,
                    'data_nascita' => $entrataPersonaData->persona->data_nascita,
                    "luogo_nascita" => $entrataPersonaData->persona->provincia_nascita,
                    "gruppo" => $entrataPersonaData->gruppoFamiliare->nome,
                    "numero_elenco" => $entrataPersonaData->persona->numero_elenco,
                    "famiglia" => ($entrataPersonaData->famiglia) ? $entrataPersonaData->famiglia->nome_famiglia : null,
                ]
            )
            ->setEvent('entrata')
            ->log(':properties.nominativo è entrato/a in Nomadelfia in data :properties.data_entrata');

    }

}
