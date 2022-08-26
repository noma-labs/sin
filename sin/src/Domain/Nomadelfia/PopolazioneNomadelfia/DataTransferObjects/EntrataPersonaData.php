<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

class EntrataPersonaData
{

    public Persona $persona;

    public string $data_entrata;

    public Posizione $posizione;

    public string $posizione_data;

    public GruppoFamiliare $gruppoFamiliare;

    public string $gruppo_data;

    public ?Stato $stato = null;

    public ?string $stato_data;

    public ?Famiglia $famiglia = null;

    public ?string $famiglia_posizione;

    public ?string $famiglia_data;

//    /**
//     * @throws \Exception
//     */
//    public static function fromRequest(EntrataPersonaRequest $request, string $persona_id): EntrataPersonaData
//    {
//        $dto = new self();
//        $dto->persona = Persona::findOrFail($persona_id);
//        $dto->data_entrata = $request->input('data_entrata');
//        if ($request->exists('gruppo_id')) {
//            $dto->gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
//        }
//        if ($request->exists('famiglia_id')) {
//            $dto->famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
//        }
//        return $dto;
//    }

}