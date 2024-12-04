<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Classe;
use App\Scuola\Requests\AddCoordinatoreRequest;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class ClassiCoordinatoriController
{
    public function store(AddCoordinatoreRequest $request, $id)
    {
        $request->validated();
        $classe = Classe::findOrFail($id);
        $coord = Persona::findOrFail($request->coord_id);

        $classe->aggiungiCoordinatore($coord, $request->data_inizio, $request->coord_tipo);

        return redirect()->back()->withSuccess("Coordiantore $coord->nominativo  aggiunto a {$classe->tipo->nome} con successo.");
    }

    public function delete(Request $request, $id, $coord_id)
    {
        $classe = Classe::findOrFail($id);
        $coord = Persona::findOrFail($coord_id);
        $classe->rimuoviCoordinatore($coord);

        return redirect()->back()->withSuccess("Coordinatore $coord->nominativo  eliminato da {$classe->tipo->nome} con successo.");
    }
}
