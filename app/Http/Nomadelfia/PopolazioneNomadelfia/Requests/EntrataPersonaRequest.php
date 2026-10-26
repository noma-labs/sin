<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class EntrataPersonaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tipologia' => ['required', 'in:dalla_nascita,minorenne_accolto,minorenne_famiglia,maggiorenne_single,maggiorenne_famiglia'],
            'data_entrata' => ['required_unless:tipologia,dalla_nascita', 'nullable', 'date'],
            'famiglia_id' => ['required_unless:tipologia,maggiorenne_single,maggiorenne_famiglia', 'nullable', 'exists:db_nomadelfia.famiglie,id'],
            'gruppo_id' => ['required_if:tipologia,maggiorenne_single,maggiorenne_famiglia', 'nullable', 'exists:db_nomadelfia.gruppi_familiari,id'],
        ];
    }

    public function messages()
    {
        return [
            'tipologia.required' => 'La tipologia di entrata è obbligatoria',
            'tipologia.in' => 'La tipologia di entrata non è valida.',
            'data_entrata.date' => 'La data di entrata non è valida.',
            'famiglia_id.exists' => 'La famiglia selezionata non esiste.',
            'gruppo_id.exists' => 'Il gruppo familiare selezionato non esiste.',
            'data_entrata.required_unless' => 'La data di entrata è obbligatoria',
            'famiglia_id.required_unless' => 'La famiglia è obbligatoria',
            'gruppo_id.required_if' => 'Il gruppo familiare  è obbligatoria',
        ];
    }
}
