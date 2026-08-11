<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StorePersonaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nominativo' => ['required', 'string'],
            'nome' => ['required', 'string'],
            'cognome' => ['required', 'string'],
            'data_nascita' => ['required', 'date'],
            'luogo_nascita' => ['required', 'string'],
            'sesso' => ['required', 'in:M,F'],
        ];
    }

    public function messages(): array
    {
        return [
            'nominativo.required' => 'Il nominativo è obbligatorio.',
            'nome.required' => 'Il nome è obbligatorio.',
            'cognome.required' => 'Il cognome è obbligatorio.',
            'data_nascita.required' => 'La data di nascita è obbligatoria.',
            'data_nascita.date' => 'La data di nascita non è valida.',
            'luogo_nascita.required' => 'Il luogo di nascita è obbligatorio.',
            'sesso.required' => 'Il sesso è obbligatorio.',
            'sesso.in' => 'Il sesso deve essere M o F.',
        ];
    }
}
