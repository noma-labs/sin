<?php

namespace Domain\Nomadelfia\Persona\Actions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

class EntrataMinorenneConFamigliaAction
{

    public function execute(Persona $persona, $data_entrata, Famiglia $famiglia = null)
    {
        $gruppo = $famiglia->gruppoFamiliareAttualeOrFail();
        $pos = Posizione::find('FIGL');
        if ($persona->isMaschio()) {
            $stato = Stato::find('CEL');
        } else {
            $stato = Stato::find('NUB');
        }
        $famiglia_data = $persona->data_nascita; // la data di entrata nella famiglia è uguale alla data di nascita
        $gruppo_data = $data_entrata;
        $pos_data = $data_entrata;
        $stato_data = $persona->data_nascita;
        $persona->entrataInNomadelfia($data_entrata, $pos->id, $pos_data, $gruppo->id, $gruppo_data, $stato->id,
            $stato_data, $famiglia->id, 'FIGLIO NATO', $famiglia_data);
    }


}