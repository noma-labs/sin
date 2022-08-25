<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use App\Nomadelfia\PopolazioneNomadelfia\Requests\EntrataPersonaRequest;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

class EntrataPersonaData
{

    public string $tipo_entrata;

    public Persona $persona;

    public ?string $data_entrata;

    public Posizione $posizione;

    public string $posizione_data;

    public GruppoFamiliare $gruppoFamiliare;

    public string $gruppo_data;

    public ?Stato $stato;

    public ?string $stato_data;

    public ?Famiglia $famiglia;

    public ?string $famiglia_posizione;

    public ?string $famiglia_data;

    /**
     * @throws \Exception
     */
    public static function fromRequest(EntrataPersonaRequest $request, string $persona_id): EntrataPersonaData
    {
        $dto = new self();
        switch ($request->tipologia) {
            case 'dalla_nascita':
                $dto->tipo_entrata= "dalla_nascita";
                break;
            case 'minorenne_accolto':
                $dto->tipo_entrata = 'minorenne_accolto';
                break;
            case 'minorenne_famiglia':
                $dto->tipo_entrata = 'minorenne_famiglia';
                break;
            case 'maggiorenne_single':
                $dto->tipo_entrata = 'maggiorenne_single';
                break;
            case 'maggiorenne_famiglia':
                $dto->tipo_entrata = 'maggiorenne_famiglia';
                break;
            default:
                throw new \Exception("La tipologia di entrata $request->tipologia non riconosiuta");
        }
        $dto->persona = Persona::findOrFail($persona_id);
        $dto->data_entrata = $request->input('data_entrata');
        if ($request->exists('gruppo_id')) {
            $dto->gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
        }
        if ($request->exists('famiglia_id')) {
            $dto->famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
        }
        return $dto;
    }

}