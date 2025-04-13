<?php

declare(strict_types=1);

namespace App\Patente\Controllers;

use App\Patente\Models\CategoriaPatente;
use App\Patente\Models\CQC;
use App\Patente\Models\Patente;
use Illuminate\Http\Request;

final class PatenteCategorieController
{
    public function update(Request $request, $numero)
    {
        $patente = Patente::findorfail($numero);
        $onlyCategories = CategoriaPatente::whereNotIn('id', CQC::all()->pluck('id'))->get()->pluck('id');

        $patente->categorie()->detach($onlyCategories);
        $patente->categorie()->attach($request->categorie);

        return redirect(route('patente.visualizza', ['numero' => $numero]))->withSuccess('Categorie aggiornate con successo.');
    }
}
