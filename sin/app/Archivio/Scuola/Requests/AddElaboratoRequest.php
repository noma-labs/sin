<?php

namespace App\Scuola\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddElaboratoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "titolo" => "required",
//            "collocazione" => "required",
        ];
    }

    public function messages()
    {
        return [
            "titolo.required" => "Il titolo è obbligatorio",
            "collocazione.required" => "La collocazione è obbligatoria",
        ];
    }

}
