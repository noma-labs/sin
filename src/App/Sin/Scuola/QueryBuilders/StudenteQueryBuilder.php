<?php

declare(strict_types=1);

namespace App\Scuola\QueryBuilders;

use App\Scuola\Models\Anno;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class StudenteQueryBuilder extends Builder
{
    public function InAnnoScolastico($anno)
    {
        if ($anno instanceof Anno) {
            $anno = $anno->id;
        }

        return $this
            ->join('db_scuola.alunni_classi', 'db_scuola.alunni_classi.persona_id', '=', 'db_nomadelfia.persone.id')
            ->join('db_scuola.classi', 'db_scuola.classi.id', '=', 'db_scuola.alunni_classi.classe_id')
            ->join('db_scuola.tipo', 'db_scuola.tipo.id', '=', 'db_scuola.classi.tipo_id')
            ->join('db_scuola.anno', 'db_scuola.anno.id', '=', 'db_scuola.classi.anno_id')
            ->where('db_scuola.anno.id', '=', $anno)
            ->orderBy('data_nascita');
    }

    public function InAnnoScolasticoPerCiclo($anno)
    {
        return $this->InAnnoScolastico($anno)
            ->select('db_scuola.tipo.ciclo', DB::raw('count(*) as alunni_count'))
            ->groupBy('db_scuola.tipo.ciclo');

    }
}
