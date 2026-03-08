<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class PersonaFamigliaController
{
    public function index($id)
    {
        $persona = Persona::findOrFail($id);
        $attuale = $persona->famigliaAttuale();
        $storico = $persona->famiglieStorico;

        return view('nomadelfia.persone.famiglia.show', compact('persona', 'attuale', 'storico'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'family_id' => ['required', 'exists:db_nomadelfia.famiglie,id'],
            'type' => ['required', 'string'],
        ], [
            'family_id.required' => 'La famiglia è obbligatoria.',
            'family_id.exists' => 'La famiglia selezionata non esiste.',
            'type.required' => 'La posizione nella famiglia è obbligatoria.',
        ]);

        $persona = Persona::findOrFail($id);
        $famiglia = Famiglia::findOrFail($request->family_id);

        // Use type to assign the correct role in the family
        switch ($request->type) {
            case 'CAPO FAMIGLIA':
                $famiglia->assegnaCapoFamiglia($persona);
                break;
            case 'MOGLIE':
                $famiglia->assegnaMoglie($persona);
                break;
            case 'FIGLIO NATO':
                $famiglia->assegnaFiglioNato($persona);
                break;
            case 'FIGLIO ACCOLTO':
                $famiglia->assegnaFiglioAccolto($persona);
                break;
            default:
                return back()->withErrors(["Posizione `{$request->type}` non riconosciuta"]);
        }

        return to_route('nomadelfia.person.show', $persona->id)
            ->with('success', $persona->nominativo.' aggiunto alla famiglia '.$famiglia->nome_famiglia.' con successo');
    }
}
