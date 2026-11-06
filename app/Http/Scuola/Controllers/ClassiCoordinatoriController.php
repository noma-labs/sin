<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use App\Scuola\Models\Classe;
use App\Scuola\Requests\AddCoordinatoreRequest;
use App\View\Components\PersonaNome;
use Illuminate\Http\Request;

final class ClassiCoordinatoriController
{
    public function store(AddCoordinatoreRequest $request, $id)
    {
        $request->validated();
        $classe = Classe::findOrFail($id);
        $coord = Persona::findOrFail($request->coord_id);

        $classe->aggiungiCoordinatore($coord, $request->data_inizio, $request->coord_tipo);

        return back()->withSuccess('Coordinatore '.PersonaNome::testo($coord)." aggiunto a {$classe->tipo->nome} con successo.");
    }

    public function delete(Request $request, $id, $coord_id)
    {
        $classe = Classe::findOrFail($id);
        $coord = Persona::findOrFail($coord_id);
        $classe->rimuoviCoordinatore($coord);

        return back()->withSuccess('Coordinatore '.PersonaNome::testo($coord)." eliminato da {$classe->tipo->nome} con successo.");
    }
}
