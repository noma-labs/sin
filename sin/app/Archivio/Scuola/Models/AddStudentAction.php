<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Persona;
use Carbon\Carbon;

class AddStudentAction
{

    public function execute(Classe $classe, Persona $alunno, $data_inizio): Persona
    {
        if (is_null($data_inizio)) {
            $data_inizio = $classe->anno->data_inizio;
        }
        if (is_string($data_inizio)) {
            $data_inizio = Carbon::parse($data_inizio);
        }
        if (is_integer($alunno)) {
            $alunno = Persona::findOrFail($alunno);
        }
        $this->addStudent($classe, $alunno, $data_inizio);

        return $alunno;
    }

    private function addStudent(Classe $classe, Persona $alunno, $data_inizio)
    {
        $classe->alunni()->attach($alunno->id, ['data_inizio' => $data_inizio]);
    }

}