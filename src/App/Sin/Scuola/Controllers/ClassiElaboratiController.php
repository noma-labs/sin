<?php

namespace App\Scuola\Controllers;

use App\Scuola\Models\Classe;

class ClassiElaboratiController
{
    public function create($id)
    {
        $classe = Classe::findOrFail($id);

        return view('scuola.classi.elaborato.create', [
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