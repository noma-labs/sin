<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Controllers;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Validation\Rule;

#[Middleware('auth')]
final class PersonaFamigliaController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function index($id)
    {
        $persona = Persona::findOrFail($id);
        $attuale = $persona->famigliaAttuale();
        $storico = $persona->famiglieStorico;

        return view('anagrafe.persone.famiglia.show', compact('persona', 'attuale', 'storico'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request, string $id)
    {
        $request->validate([
            'family_id' => ['required', 'exists:db_nomadelfia.famiglie,id'],
            'type' => ['required', Rule::in(['FIGLIO NATO', 'FIGLIO ACCOLTO'])],
        ], [
            'family_id.required' => 'La famiglia è obbligatoria.',
            'family_id.exists' => 'La famiglia selezionata non esiste.',
            'type.required' => 'La posizione nella famiglia è obbligatoria.',
            'type.in' => 'La posizione deve essere figlio nato o figlio accolto. Capofamiglia e moglie si assegnano con un nuovo matrimonio.',
        ]);

        $persona = Persona::query()->findOrFail($id);
        $famiglia = Famiglia::query()->findOrFail($request->integer('family_id'));

        if ($request->string('type')->toString() === 'FIGLIO NATO') {
            $famiglia->assegnaFiglioNato($persona);
        } else {
            $famiglia->assegnaFiglioAccolto($persona);
        }

        return to_route('nomadelfia.person.show', $persona->id)
            ->with('success', $persona->nominativo.' aggiunto alla famiglia '.$famiglia->nome_famiglia.' con successo');
    }
}
