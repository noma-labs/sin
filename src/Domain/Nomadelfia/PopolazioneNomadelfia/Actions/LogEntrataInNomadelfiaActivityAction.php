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
                    'data_entrata' => $entrataPersonaData->data_entrata,
                    'data_nascita' => $entrataPersonaData->persona->data_nascita,
                    "luogo_nascita" => $entrataPersonaData->persona->provincia_nascita,
                    "gruppo" => $entrataPersonaData->gruppoFamiliare->nome,
                    "numero_elenco" => $entrataPersonaData->persona->numero_elenco,
                    "famiglia" => ($entrataPersonaData->famiglia) ? $entrataPersonaData->famiglia->nome_famiglia : null,
                ]
            )
            ->log('entrata');

    }

}
