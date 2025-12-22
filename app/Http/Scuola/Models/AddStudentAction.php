<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use App\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;

final class AddStudentAction
{
    public function execute(Classe $classe, Persona|int $alunno, $data_inizio): Persona
    {
        if (is_null($data_inizio)) {
            $data_inizio = $classe->anno->data_inizio;
        }
        if (is_string($data_inizio)) {
            $data_inizio = \Illuminate\Support\Facades\Date::parse($data_inizio);
        }
        if (is_int($alunno)) {
            $alunno = Persona::findOrFail($alunno);
        }
        $this->addStudent($classe, $alunno, $data_inizio);

        return $alunno;
    }

    private function addStudent(Classe $classe, Persona $alunno, $data_inizio): void
    {
        $classe->alunni()->attach($alunno->id, ['data_inizio' => $data_inizio]);
    }
}
