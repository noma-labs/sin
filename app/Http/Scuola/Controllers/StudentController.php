<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Classe;
use App\Scuola\Models\Elaborato;
use App\Scuola\Models\Studente;
use Illuminate\Support\Facades\DB;

final class StudentController
{
    public function show($id)
    {
        $student = Studente::select('id', 'nome', 'cognome', 'data_nascita', 'cf', 'sesso','id_alfa_enrico')->findOrFail($id);

        $famigliaEnrico = DB::connection('db_nomadelfia')
                    ->table('alfa_enrico_15_feb_23')
                    ->select('famiglia')
                    ->where('id', $student->id_alfa_enrico)
                    ->first();

        $classes = Classe::select('classi.id', 'tipo.nome as tipo_nome', 'tipo.ciclo as tipo_ciclo', 'anno.id as anno_id', 'anno.scolastico as anno_scolastico')
            ->join('tipo', 'classi.tipo_id', '=', 'tipo.id')
            ->join('anno', 'classi.anno_id', '=', 'anno.id')
            ->join('alunni_classi', 'classi.id', '=', 'alunni_classi.classe_id')
            ->where('alunni_classi.persona_id', $student->id)
            ->orderBy('anno.scolastico', 'ASC')
            ->get();

        $works = Elaborato::select('elaborati.id', 'elaborati.titolo', 'elaborati.anno_scolastico', 'elaborati.classi')
            ->join('elaborati_studenti', 'elaborati.id', '=', 'elaborati_studenti.elaborato_id')
            ->where('elaborati_studenti.studente_id', $student->id)
            ->orderBy('elaborati.anno_scolastico', 'ASC')
            ->get();

        return view('scuola.student.show', compact('student', 'classes', 'works','famigliaEnrico'));
    }
}
