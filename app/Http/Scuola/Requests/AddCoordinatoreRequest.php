<?php

declare(strict_types=1);

namespace App\Scuola\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AddCoordinatoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coord_id' => ['required'],
            'coord_tipo' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'coord_id.required' => 'Coordinatore è obbligatorio',
            'coord_tipo.required' => 'La tipologia di coordinatore è obbligatoria',
        ];
    }
}
