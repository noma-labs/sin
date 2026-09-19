<?php

declare(strict_types=1);

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
final class CapogruppoController
{
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'nuovo' => ['required', 'integer', 'exists:db_nomadelfia.persone,id'],
            'inizio' => ['required', 'date'],
        ], [
            'nuovo.required' => 'Il nuovo capogruppo è abbligatoripo',
            'inizio.required' => 'La data di inizio è obbligatoria',
        ]);
        $gruppo = GruppoFamiliare::findOrFail($id);
        $gruppo->assegnaCapogruppo((int) $validated['nuovo'], Carbon::parse($validated['inizio']));

        return back()->withSuccess('NUovo capogruppo inserito con successo');
    }
}
