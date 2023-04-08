<?php

namespace App\Scuola\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
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
