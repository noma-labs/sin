<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\DataTransferObjects\Dimensione;
use App\Scuola\Models\Elaborato;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class StudentController
{

    public function show($id)
    {
        $student = Studente::with('classe', 'classe.anno', 'classe.tipo', 'elaborato')
                ->findOrFail($id);

        return view('scuola.student.show', ['student' => $student]);
    }

}
