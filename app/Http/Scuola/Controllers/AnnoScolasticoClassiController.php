<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Illuminate\Http\Request;

final class AnnoScolasticoClassiController
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'tipo' => 'required',
        ], [
            'tipo.required' => 'Il tipo di classe da aggiungere è obbligatorio.',
        ]);
        $anno = Anno::FindOrFail($id);
        $classe = $anno->aggiungiClasse(ClasseTipo::findOrFail($request->tipo));

        return redirect()->back()->withSuccess("Classe  {$classe->tipo->nome} aggiunta a {{$anno->scolastico}} con successo.");
    }
}
