<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Exceptions\PersonaIsMinorenne;
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

    public function execute(Persona $persona, $data_entrata, GruppoFamiliare $gruppo): void
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
        $this->calcFamiglia($dto);

        $this->entrataInNomadelfiaAction->execute($dto);

    }

    public function calcFamiglia(EntrataPersonaData $dto): void
    {
        $dto->famiglia_posizione = '';
    }

    public function calcPosizione(EntrataPersonaData $dto): void
    {
        $dto->posizione = Posizione::find('OSPP');
        $dto->posizione_data = $dto->data_entrata;
    }

    public function calcStato(EntrataPersonaData $dto): void
    {
        $dto->stato_data = $dto->persona->data_nascita;
    }

    public function calcGruppoFamiliare(EntrataPersonaData $dto): void
    {
        $dto->gruppo_data = $dto->data_entrata;
    }
}
