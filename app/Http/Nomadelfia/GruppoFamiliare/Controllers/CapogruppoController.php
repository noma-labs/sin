<?php

declare(strict_types=1);

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Illuminate\Http\Request;

final class CapogruppoController
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'nuovo' => 'required',
            'inizio' => 'required',
        ], [
            'nuovo.required' => 'Il nuovo capogruppo è abbligatoripo',
            'inizio.required' => 'La data di inizio è obbligatoria',
        ]);
        $gruppo = GruppoFamiliare::findOrFail($id);
        $gruppo->assegnaCapogruppo($request->nuovo, $request->inizio);

        return redirect()->back()->withSuccess('NUovo capogruppo inserito con successo');
    }
}
