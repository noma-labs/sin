<?php

declare(strict_types=1);

namespace App\Scuola\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AddStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //            "alunno_id" => "required",
        ];
    }

    public function messages()
    {
        return [
            //                "alunno_id.required" => "L'Alunno è obbligatorio",
        ];
    }
}
