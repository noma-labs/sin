<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;
use App\Nomadelfia\Actions\EntrataInNomadelfiaAction;
use Carbon\Carbon;

class EntrataNatoAction
{
    private $entrataInNomadelfiaAction;

    public function __construct(EntrataInNomadelfiaAction $entrataInNomadelfiaAction)
    {
        $this->entrataInNomadelfiaAction = $entrataInNomadelfiaAction;
    }

    public function execute(Persona $persona, Famiglia $famiglia): Persona
    {
        $gruppo = $famiglia->gruppoFamiliareAttualeOrFail();

        $pos = Posizione::find("FIGL");
        if ($persona->isMaschio()) {
            $stato = Stato::find("CEL");
        } else {
            $stato = Stato::find("NUB");
        }
        $famiglia_data = $persona->data_nascita;
        $gruppo_data = $persona->data_nascita;
        $pos_data = $persona->data_nascita;
        $stato_data = $persona->data_nascita;

        $this->entrataInNomadelfiaAction->execute($persona, $persona->data_nascita, $pos, $pos_data, $gruppo,
            $gruppo_data, $stato, $stato_data, $famiglia, "FIGLIO NATO", $famiglia_data);
    }


}