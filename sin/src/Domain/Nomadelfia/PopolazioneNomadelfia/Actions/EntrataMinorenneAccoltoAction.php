<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

class EntrataMinorenneAccoltoAction
{
    private EntrataInNomadelfiaAction $entrataInNomadelfiaAction;

    public function __construct(
        EntrataInNomadelfiaAction $entrataInNomadelfiaAction
    ) {
        $this->entrataInNomadelfiaAction = $entrataInNomadelfiaAction;
    }

    public function execute(Persona $persona, $data_entrata, Famiglia $famiglia)
    {
        $gruppo = $famiglia->gruppoFamiliareAttualeOrFail();

        $gruppo= GruppoFamiliare::findOrFail($gruppo->id);
        $pos = Posizione::find('FIGL');
        if ($persona->isMaschio()) {
            $stato = Stato::find('CEL');
        } else {
            $stato = Stato::find('NUB');
        }
        $famiglia_data = $data_entrata;  // la data di entrata nella famiglia è uguale alla data di entrata in nomadelfia
        $gruppo_data = $data_entrata;
        $pos_data = $data_entrata;
        $stato_data = $persona->data_nascita;
        $this->entrataInNomadelfiaAction->execute($persona,
            $data_entrata,
            $pos,
            $pos_data,
            $gruppo,
            $gruppo_data,
            $stato,
            $stato_data,
            $famiglia,
            'FIGLIO ACCOLTO',
            $famiglia_data);
    }


}