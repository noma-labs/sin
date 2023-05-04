<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;

class LogDecessoPersonaAction
{
    public function execute(Persona $persona, string $data_deceso)
    {
        activity('nomadelfia')
            ->performedOn($persona)
            ->withProperties([
                'nominativo' => $persona->nominativo,
                'luogo_nascita' => $persona->provincia_nascita,
                'data_nascita' => $persona->data_nascita,
                'data_decesso' => $data_deceso,
                'numero_elenco' => $persona->numero_elenco,
            ]
            )
            ->setEvent('popolazione.decesso')
            ->log('Persona deceduta in data :properties.data_decesso');

    }
}
