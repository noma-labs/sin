<?php

namespace App\Scuola\Controllers;

use App\Scuola\Models\AddStudentAction;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Classe;
use App\Scuola\Requests\AddCoordinatoreRequest;
use App\Scuola\Requests\AddStudentRequest;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class ClassiElaboratiController
{

    public function create($id)
    {
        $classe = Classe::findOrFail($id);

        return view('scuola.classi.elaborato.create',[
                'classe' => $classe,
                'rilegature' => [
                    'Altro',
                    'Anelli',
                    'Brossura (Copertina Flessibile)',
                    'Cartonata (Copertina Rigida)',
                    'Filo Refe',
                    'Punto Metallico (Spillatura)',
                    'Spirale',
                    'Termica',
                ],
    ]);

    }
}
