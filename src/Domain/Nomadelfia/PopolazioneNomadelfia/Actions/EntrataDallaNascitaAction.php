<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
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
        $dto = new EntrataPersonaData();
        $dto->famiglia = $famiglia;
        $dto->persona = $persona;
        $dto->data_entrata = $dto->persona->data_nascita;

        $this->calcGruppoFamiliare($dto);
        $this->calcPosizione($dto);
        $this->calcStato($dto);
        $this->calcFamiglia($dto);

        $this->entrataInNomadelfiaAction->execute($dto);
    }

    public function calcFamiglia(EntrataPersonaData $dto)
    {
        $dto->famiglia_data = $dto->persona->data_nascita;
        $dto->famiglia_posizione = Famiglia::getFiglioNatoEnum();
    }

    public function calcGruppoFamiliare(EntrataPersonaData $dto)
    {
        $gruppo = $dto->famiglia->gruppoFamiliareAttualeOrFail();
        $dto->gruppoFamiliare = GruppoFamiliare::findOrFail($gruppo->id);
        $dto->gruppo_data = $dto->persona->data_nascita;
    }

    public function calcPosizione(EntrataPersonaData $dto)
    {
        $dto->posizione = Posizione::find('FIGL');
        $dto->posizione_data = $dto->persona->data_nascita;
    }

    public function calcStato(EntrataPersonaData $dto)
    {
        if ($dto->persona->isMaschio()) {
            $dto->stato = Stato::find('CEL');
        } else {
            $dto->stato = Stato::find('NUB');
        }
        $dto->stato_data = $dto->persona->data_nascita;
    }
}
