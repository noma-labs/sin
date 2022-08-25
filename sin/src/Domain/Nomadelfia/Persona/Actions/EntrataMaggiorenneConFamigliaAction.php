<?php

namespace Domain\Nomadelfia\Persona\Actions;

use App\Nomadelfia\Exceptions\PersonaIsMinorenne;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Illuminate\Support\Str;

class EntrataMaggiorenneConFamigliaAction
{
    private EntrataInNomadelfiaAction $entrataInNomadelfiaAction;

    public function __construct(
        EntrataInNomadelfiaAction $entrataInNomadelfiaAction
    ) {
        $this->entrataInNomadelfiaAction = $entrataInNomadelfiaAction;
    }

    public function execute(Persona $persona, $data_entrata, GruppoFamiliare $gruppo)
    {
        if (!$persona->isMaggiorenne()) {
            throw PersonaIsMinorenne::named($persona->nominativo);
        }
        $pos = Posizione::find('OSPP');
        $gruppo_data = $data_entrata;
        $pos_data = $data_entrata;
        $stato_data = $persona->data_nascita;
        $this->entrataInNomadelfiaAction->execute($persona,
            $data_entrata,
            $pos,
            $pos_data,
            $gruppo,
            $gruppo_data);

    }


}