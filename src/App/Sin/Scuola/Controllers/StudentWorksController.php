<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
use App\Scuola\Models\Studente;

final class StudentWorksController
{
    public function show($id)
    {
        $student = Studente::select('id', 'nome', 'cognome', 'data_nascita', 'cf', 'sesso')->findOrFail($id);

        $works = Elaborato::select('elaborati.id', 'elaborati.titolo', 'elaborati.anno_scolastico', 'elaborati.classi')
            ->join('elaborati_studenti', 'elaborati.id', '=', 'elaborati_studenti.elaborato_id')
            ->where('elaborati_studenti.studente_id', $id)
            ->orderBy('elaborati.anno_scolastico', 'ASC')
            ->get();

        return view('scuola.student.works.show', compact('works', 'student'));
    }
}
