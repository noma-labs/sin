<?php

namespace Domain\Nomadelfia\Persona\Actions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

class EntrataDallaNascitaAction
{
    private EntrataInNomadelfiaAction $entrataInNomadelfiaAction;

    public function __construct(
        EntrataInNomadelfiaAction $entrataInNomadelfiaAction
    ) {
        $this->entrataInNomadelfiaAction = $entrataInNomadelfiaAction;
    }

    public function execute(Persona $persona, Famiglia $famiglia)
    {
        $gruppo = $famiglia->gruppoFamiliareAttualeOrFail();
        $gruppo= GruppoFamiliare::findOrFail($gruppo->id);

        $pos = Posizione::find('FIGL');
        if ($persona->isMaschio()) {
            $stato = Stato::find('CEL');
        } else {
            $stato = Stato::find('NUB');
        }
        $famiglia_data = $persona->data_nascita;
        $gruppo_data = $persona->data_nascita;
        $pos_data = $persona->data_nascita;
        $stato_data = $persona->data_nascita;
        $this->entrataInNomadelfiaAction->execute($persona,
            $persona->data_nascita,
            $pos,
            $pos_data,
            $gruppo,
            $gruppo_data,
            $stato,
            $stato_data,
            $famiglia,
            'FIGLIO NATO',
            $famiglia_data);
    }


}