<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Exceptions\PersonaIsMinorenne;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Origine as ModelsOrigine;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Carbon\Carbon;

final readonly class EntrataMaggiorenneConFamigliaAction
{
    public function __construct(private EntrataPersonaAction $entrataInNomadelfiaAction) {}

    public function execute(Persona $persona, Carbon $data_entrata, GruppoFamiliare $gruppo): void
    {
        if (! $persona->isMaggiorenne()) {
            throw PersonaIsMinorenne::named($persona->nominativo);
        }
        $dto = new EntrataPersonaData;
        $dto->persona = $persona;
        $dto->data_entrata = $data_entrata;
        $dto->gruppoFamiliare = $gruppo;
        $dto->origine = ModelsOrigine::Esterno;

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
        $dto->stato_data = \Illuminate\Support\Facades\Date::parse($dto->persona->data_nascita);
    }

    public function calcGruppoFamiliare(EntrataPersonaData $dto): void
    {
        $dto->gruppo_data = $dto->data_entrata;
    }
}
