<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Exceptions\PersonaIsMaggiorenne;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\EntrataPersonaData;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Origine;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Carbon\Carbon;

final readonly class EntrataMinorenneConFamigliaAction
{
    public function __construct(private EntrataPersonaAction $entrataInNomadelfiaAction) {}

    public function execute(Persona $persona, Carbon $data_entrata, ?Famiglia $famiglia = null): void
    {
        // the age at the entrata: an entrata can be recorded years later, when the person is an adult
        if (\Illuminate\Support\Facades\Date::parse($persona->data_nascita)->addYears(18)->lessThanOrEqualTo($data_entrata)) {
            throw PersonaIsMaggiorenne::named($persona->nominativo);
        }

        $dto = new EntrataPersonaData;
        $dto->persona = $persona;
        $dto->data_entrata = $data_entrata;
        $dto->famiglia = $famiglia;
        $dto->origine = Origine::MinorenneConFamiglia;

        $this->calcGruppoFamiliare($dto);
        $this->calcPosizione($dto);
        $this->calcFamiglia($dto);

        $this->entrataInNomadelfiaAction->execute($dto);
    }

    public function calcFamiglia(EntrataPersonaData $dto): void
    {
        $dto->famiglia_posizione = Famiglia::getFiglioNatoEnum();
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
}
