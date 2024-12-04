<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class EntrataPersonaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tipologia' => 'required',
            'data_entrata' => 'required_unless:tipologia,dalla_nascita',
            'famiglia_id' => 'required_unless:tipologia,maggiorenne_single,maggiorenne_famiglia',
            'gruppo_id' => 'required_if:tipologia,maggiorenne_single,maggiorenne_famiglia',
        ];
    }

    public function messages()
    {
        return [
            'tipologia.required' => 'La tipologia di entrata è obbligatoria',
            'data_entrata.required_unless' => 'La data di entrata è obbligatoria',
            'famiglia_id.required_unless' => 'La famiglia è obbligatoria',
            'gruppo_id.required_if' => 'Il gruppo familiare  è obbligatoria',
        ];
    }
}
