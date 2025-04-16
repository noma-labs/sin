<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Actions\ProposeNumeroElencoAction;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class FolderNumberController
{
    public function create($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $first = $persona->getInitialLetterOfCogonome();
        $assegnati = Persona::NumeroElencoPrefixByLetter($first)->get();

        $action = new ProposeNumeroElencoAction;
        $propose = $action->execute($persona);

        return view('nomadelfia.persone.edit_numero_elenco', compact('persona', 'first', 'assegnati', 'propose'));
    }

    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'numero_elenco' => 'required',
        ], [
            'numero_elenco.required' => 'Il numero di elenco è obbligatorio',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $ne = $request->get('numero_elenco');
        if ($persona->numero_elenco) {
            return redirect()->back()->withError("La persona $persona->nominativo ha già un numero di elenco: $persona->numero_elenco.");
        }
        $persona->update(['numero_elenco' => $ne]);

        return redirect()->route('nomadelfia.person.show', $idPersona)->withSuccess("Numero di elenco di  $persona->nominativo assegnato correttamente.");
    }
}
