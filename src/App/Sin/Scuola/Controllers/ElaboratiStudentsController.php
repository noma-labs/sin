<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolasticoData;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Elaborato;
use Illuminate\Http\Request;

final class ElaboratiStudentsController
{
    public function create(Request $request, $id)
    {
        $elaborato = Elaborato::findOrFail($id);

        $a = Anno::where('scolastico', $elaborato->anno_scolastico)->firstOrFail();

        $anno = AnnoScolasticoData::FromDatabase($a);

        return view('scuola.elaborati.students.create', compact('anno'));

    }
}
