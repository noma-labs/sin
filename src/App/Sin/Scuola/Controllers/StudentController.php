<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Studente;

final class StudentController
{
    public function show($id)
    {
        $student = Studente::with('classe', 'classe.anno', 'classe.tipo', 'elaborato')
            ->findOrFail($id);

        return view('scuola.student.show', ['student' => $student]);
    }
}
