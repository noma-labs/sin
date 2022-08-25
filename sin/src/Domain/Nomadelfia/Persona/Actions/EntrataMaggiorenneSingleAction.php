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

class EntrataMaggiorenneSingleAction
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
        if ($persona->isMaschio()) {
            $stato = Stato::find('CEL');
        } else {
            $stato = Stato::find('NUB');
        }
        $gruppo_data = $data_entrata;
        $pos_data = $data_entrata;
        $stato_data = $persona->data_nascita;

        $fam_data = Carbon::parse($persona->data_nascita)->addYears(18)->toDatestring();
        $nome_famiglia = $persona->nome . ' ' . Str::substr($persona->cognome, 0, 2);
        $fam = Famiglia::firstOrCreate(['nome_famiglia' => $nome_famiglia], ['data_creazione' => $fam_data]);

        $this->entrataInNomadelfiaAction->execute($persona,
            $data_entrata,
            $pos,
            $pos_data,
            $gruppo,
            $gruppo_data,
            $stato,
            $stato_data,
            $fam,
            'SINGLE',
            $fam_data);
    }


}