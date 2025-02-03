<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Classe;
use App\Scuola\Models\Studente;

final class StudentClassesController
{
    public function show($id)
    {
        $student = Studente::select('id', 'nome', 'cognome', 'data_nascita', 'cf', 'sesso')->findOrFail($id);

        $classes = Classe::select('classi.id', 'tipo.nome as tipo_nome', 'tipo.ciclo as tipo_ciclo', 'anno.id as anno_id', 'anno.scolastico as anno_scolastico')
            ->join('tipo', 'classi.tipo_id', '=', 'tipo.id')
            ->join('anno', 'classi.anno_id', '=', 'anno.id')
            ->join('alunni_classi', 'classi.id', '=', 'alunni_classi.classe_id')
            ->where('alunni_classi.persona_id', $student->id)
            ->orderBy('anno.scolastico', 'ASC')
            ->get();

        return view('scuola.student.class.show', compact('student', 'classes'));
    }
}
