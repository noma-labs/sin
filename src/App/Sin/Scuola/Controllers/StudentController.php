<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\DataTransferObjects\Dimensione;
use App\Scuola\Models\Elaborato;
use App\Scuola\Models\Studente;

final class StudentController
{

    public function show($id)
    {
        $student = Studente::findOrFail($id);
        $student->load('classe')->toSql();
        return view('scuola.student.show', ['student' => $student]);
    }
}
