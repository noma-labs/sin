<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
use Illuminate\Http\Request;

final class ElaboratiStudentsController
{
    public function create(Request $request, $id)
    {
        $elaborato = Elaborato::findOrFail($id);

        return view('scuola.elaborati.students.create', compact('elaborato'));

    }

    public function store(Request $request, $id)
    {

        $elaborato = Elaborato::findOrFail($id);
        $elaborato->studenti()->sync($request->students);

        return redirect()->route('scuola.elaborati.show', [$id => $id])->withSuccess('Studenti aggiunti correttamente');
    }
}
