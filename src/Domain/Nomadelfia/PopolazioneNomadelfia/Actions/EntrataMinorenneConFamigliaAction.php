<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

class EntrataMinorenneConFamigliaAction
{
    private SaveEntrataInNomadelfiaAction $entrataInNomadelfiaAction;

    public function __construct(
        SaveEntrataInNomadelfiaAction $entrataInNomadelfiaAction
    ) {
        $this->entrataInNomadelfiaAction = $entrataInNomadelfiaAction;
    }

    public function execute(Persona $persona, $data_entrata, Famiglia $famiglia = null)
    {
        $dto = new EntrataPersonaData();
        $dto->persona = $persona;
        $dto->data_entrata = $data_entrata;
        $dto->famiglia = $famiglia;

        $this->calcStato($dto);
        $this->calcGruppoFamiliare($dto);
        $this->calcPosizione($dto);
        $this->calcFamiglia($dto);


        $this->entrataInNomadelfiaAction->execute($dto);
    }

    public function calcFamiglia(EntrataPersonaData $dto)
    {
        $dto->famiglia_data = $dto->persona->data_nascita; // la data di entrata nella famiglia è uguale alla data di nascita
        $dto->famiglia_posizione = Famiglia::getFiglioNatoEnum();
    }


    public function calcGruppoFamiliare(EntrataPersonaData $dto)
    {
        $gruppo = $dto->famiglia->gruppoFamiliareAttualeOrFail();
        $dto->gruppoFamiliare = GruppoFamiliare::findOrFail($gruppo->id);
        $dto->gruppo_data = $dto->data_entrata;
    }


    public function calcPosizione(EntrataPersonaData $dto)
    {
        $dto->posizione = Posizione::find('FIGL');;
        $dto->posizione_data = $dto->data_entrata;
    }

    public function calcStato(EntrataPersonaData $dto)
    {
        $dto->stato_data = $dto->persona->data_nascita;
        if ($dto->persona->isMaschio()) {
            $dto->stato = Stato::find('CEL');
        } else {
            $dto->stato = Stato::find('NUB');
        }
    }


}