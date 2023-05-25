<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaNumeroElencoController extends CoreBaseController
{
    public function edit($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $first = $persona->getInitialLetterOfCogonome();
        $assegnati = Persona::NumeroElencoPrefixByLetter($first)->get();
        $propose = $persona->proposeNumeroElenco();

        return view('nomadelfia.persone.edit_numero_elenco', compact('persona', 'first', 'assegnati', 'propose'));
    }

    public function update(Request $request, $idPersona)
    {
        $validatedData = $request->validate([
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

        return redirect()->route('nomadelfia.persone.dettaglio',
            ['idPersona' => $idPersona])->withSuccess("Numero di elenco di  $persona->nominativo assegnato correttamente.");
    }
}
