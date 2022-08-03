<?php

namespace App\Scuola\QueryBuilders;

use App\Scuola\Models\Anno;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudenteQueryBuilder extends Builder
{
    public function InAnnoScolastico($anno)
    {
        if ($anno instanceof Anno) {
            $anno = $anno->id;
        }
        $students = $this
            ->join('db_scuola.alunni_classi', 'db_scuola.alunni_classi.classe_id', "=", 'db_nomadelfia.persone.id')
            ->join('db_scuola.classi', "db_scuola.classi.id", "=", "db_scuola.alunni_classi.classe_id")
            ->join('db_scuola.tipo', 'db_scuola.tipo.id', "=", 'db_scuola.classi.tipo_id')
            ->join('db_scuola.anno', 'db_scuola.anno.id', "=", 'db_scuola.classi.anno_id')
            ->whereNull('db_scuola.alunni_classi.data_fine')
            ->where('db_scuola.anno.id', '=', $anno)
            ->orderBy('data_nascita');
        return $students;
    }

    public function InAnnoScolasticoPerCiclo($anno){
        $query = $this->InAnnoScolastico($anno);
        $cicloAlunni = $query
            ->select('db_scuola.tipo.ciclo', DB::raw('count(*) as count'))
            ->groupBy('db_scuola.tipo.ciclo');
        return $cicloAlunni;
    }

}