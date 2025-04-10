<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Origine;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

final readonly class EntrataMinorenneAccoltoAction
{
    public function __construct(private EntrataPersonaAction $entrataInNomadelfiaAction) {}

    public function execute(Persona $persona, Carbon $data_entrata, Famiglia $famiglia): void
    {
        $dto = new EntrataPersonaData;
        $dto->persona = $persona;
        $dto->data_entrata = $data_entrata;
        $dto->famiglia = $famiglia;
        $dto->origine = Origine::Accolto;

        $this->calcStato($dto);
        $this->calcGruppoFamiliare($dto);
        $this->calcPosizione($dto);
        $this->calcFamiglia($dto);

        $this->entrataInNomadelfiaAction->execute($dto);
    }

    public function calcFamiglia(EntrataPersonaData $dto): void
    {
        $dto->famiglia_posizione = Famiglia::getFiglioAccoltoEnum();
    }

    public function calcGruppoFamiliare(EntrataPersonaData $dto): void
    {
        $gruppo = $dto->famiglia->gruppoFamiliareAttualeOrFail();
        $dto->gruppoFamiliare = GruppoFamiliare::findOrFail($gruppo->id);
        $dto->gruppo_data = $dto->data_entrata;
    }

    public function calcPosizione(EntrataPersonaData $dto): void
    {
        $dto->posizione = Posizione::find('FIGL');
        $dto->posizione_data = $dto->data_entrata;
    }

    public function calcStato(EntrataPersonaData $dto): void
    {
        $dto->stato_data = Carbon::parse($dto->persona->data_nascita);
        if ($dto->persona->isMaschio()) {
            $dto->stato = Stato::find('CEL');
        } else {
            $dto->stato = Stato::find('NUB');
        }
    }
}
