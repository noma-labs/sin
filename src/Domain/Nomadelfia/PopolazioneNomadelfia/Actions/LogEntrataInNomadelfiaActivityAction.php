<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;

class LogEntrataInNomadelfiaActivityAction
{
    public function execute(Persona $persona, string $data_entrata, GruppoFamiliare $gruppo, Famiglia|null $famiglia)
    {
        activity('nomadelfia')
            ->performedOn($persona)
            ->withProperties([
                'nominativo' => $persona->nominativo,
                'data_entrata' => $data_entrata,
                'data_nascita' => $persona->data_nascita,
                'luogo_nascita' => $persona->provincia_nascita,
                'gruppo' => $gruppo->nome,
                'numero_elenco' => $persona->numero_elenco,
                'famiglia' => ($famiglia) ? $famiglia->nome_famiglia : null,
            ]
            )
            ->setEvent('popolazione.entrata')
            ->log('Nuova entrata in Nomadelfia in data :properties.data_entrata');
    }
}
