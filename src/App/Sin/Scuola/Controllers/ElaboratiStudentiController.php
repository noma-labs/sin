<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\DataTransferObjects\Dimensione;
use App\Scuola\Models\Classe;
use App\Scuola\Models\Elaborato;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ElaboratiStudentiController
{
    public function edit(int $id)
    {
        $elaborato = Elaborato::findOrFail($id);

        $as = AnnoScolastico::fromString($elaborato->anno_scolastico);

        $students = Classe::join('alunni_classi', 'studenti.classe_id', '=', 'classi.id')
                ->join('tipo', 'tipo.id', '=', 'classi.tipo_id')
                ->join('anno', 'anno.id', '=', 'classi.anno_id')
                ->select('anno.scolastico', 'tipo.nome', 'tipo.ciclo', 'classi.id')
                ->where('anno.scolastico', $as->toString())
                ->orderBy('anno.scolastico')
                ->orderBy('tipo.ciclo')
                ->orderBy('tipo.nome')
                ->get();

        return view('scuola.elaborati.students.edit', [
            'elaborati' => $elaborato,
        ]);
    }

}
