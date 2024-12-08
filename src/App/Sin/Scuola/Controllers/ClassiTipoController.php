<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\AddStudentAction;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Classe;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Requests\AddStudentRequest;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class ClassiTipoController
{
    public function update(Request $request, int $id)
    {
        $request->validate([
            'tipo_id' => 'required',
        ], [
            'tipo_id.required' => 'Il tipo di classe è obbligatorio.',
        ]);

        $classe = Classe::findOrFail($id);
        $tipo = ClasseTipo::findOrFail($request->tipo_id);
        $classe->tipo()->associate($tipo);
        $classe->save();

        return redirect()->back()->withSuccess('Classe aggiornata con successo.');

    }
}
