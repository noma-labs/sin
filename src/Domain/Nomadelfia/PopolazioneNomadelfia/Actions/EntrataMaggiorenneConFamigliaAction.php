<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Exceptions\PersonaIsMinorenne;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;

class EntrataMaggiorenneConFamigliaAction
{
    private EntrataPersonaAction $entrataInNomadelfiaAction;

    public function __construct(
        EntrataPersonaAction $entrataInNomadelfiaAction
    ) {
        $this->entrataInNomadelfiaAction = $entrataInNomadelfiaAction;
    }

    public function execute(Persona $persona, $data_entrata, GruppoFamiliare $gruppo, Persona $capo_famiglia)
    {
        if (! $persona->isMaggiorenne()) {
            throw PersonaIsMinorenne::named($persona->nominativo);
        }
        $dto = new EntrataPersonaData();
        $dto->persona = $persona;
        $dto->data_entrata = $data_entrata;
        $dto->gruppoFamiliare = $gruppo;

        $this->calcStato($dto);
        $this->calcGruppoFamiliare($dto);
        $this->calcPosizione($dto);
        $this->calcFamiglia($dto, $capo_famiglia);

        $this->entrataInNomadelfiaAction->execute($dto);

    }

    public function calcFamiglia(EntrataPersonaData $dto, Persona $capo_famiglia)
    {
        $famiglia = $capo_famiglia->famigliaAttuale();
        if ($famiglia == null) {
            $famiglia = Famiglia::create(['nome_famiglia' => $capo_famiglia->nominativo]);
        }
        $dto->famiglia = $famiglia;
        if ($dto->persona->id == $capo_famiglia->id) {
            $dto->famiglia_posizione = Famiglia::getCapoFamigliaEnum();

            return;
        }

        if (! $dto->persona->isMaschio() and $dto->persona->isMaggiorenne()) {
            $dto->famiglia_posizione = Famiglia::getMoglieEnum();

            return;
        }
        // a "wrong" assumption: a son of a family coming from outside is always "FIGLIO NATO".
        $dto->famiglia_posizione = Famiglia::getFiglioNatoEnum();

    }

    public function calcPosizione(EntrataPersonaData $dto)
    {
        $dto->posizione = Posizione::find('OSPP');
        $dto->posizione_data = $dto->data_entrata;
    }

    public function calcStato(EntrataPersonaData $dto)
    {
        $dto->stato_data = $dto->persona->data_nascita;
    }

    public function calcGruppoFamiliare(EntrataPersonaData $dto)
    {
        $dto->gruppo_data = $dto->data_entrata;
    }
}
