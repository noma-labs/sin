<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Controllers;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Http\RedirectResponse;
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
        $attuali = $persona->famiglieAttuali()->withPivot('posizione_famiglia')->get();
        $storico = $persona->famiglieStorico;

        return view('anagrafe.persone.famiglia.show', compact('persona', 'attuali', 'storico'));
    }

    /**
     * Resolves a persona in the nucleo of more than one famiglia: the chosen famiglia stays the famiglia attuale,
     * the persona leaves the nucleo of the others, which pass in the storico.
     */
    #[Middleware('can:popolazione.persona.modifica')]
    public function updateAttuale(Request $request, string $id): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);
        $request->validate([
            'famiglia_id' => ['required', Rule::in($persona->famiglieAttuali()->pluck('famiglie.id')->all())],
        ], [
            'famiglia_id.required' => 'La famiglia attuale è obbligatoria.',
            'famiglia_id.in' => 'La famiglia scelta non è una famiglia attuale della persona.',
        ]);

        $famiglia = Famiglia::query()->findOrFail($request->integer('famiglia_id'));
        $persona->famiglieAttuali()
            ->where('famiglie.id', '!=', $famiglia->id)
            ->get()
            ->each(fn (Famiglia $altra) => $altra->uscitaDalNucleoFamiliare($persona));

        return to_route('anagrafe.person.families', $persona->id)
            ->with('success', "{$famiglia->nome_famiglia} è ora la famiglia attuale di {$persona->nominativo}");
    }

    /**
     * @return RedirectResponse
     */
    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request, string $id)
    {
        $request->validate([
            'family_id' => ['required', 'exists:db_nomadelfia.famiglie,id'],
            'type' => ['required', Rule::in(['FIGLIO NATO', 'FIGLIO ACCOLTO', 'AGGREGATO'])],
            'data_entrata' => ['nullable', 'date'],
        ], [
            'family_id.required' => 'La famiglia è obbligatoria.',
            'family_id.exists' => 'La famiglia selezionata non esiste.',
            'type.required' => 'La posizione nella famiglia è obbligatoria.',
            'type.in' => 'La posizione deve essere figlio nato, figlio accolto o aggregato. Capofamiglia e moglie si assegnano con un nuovo matrimonio.',
            'data_entrata.date' => 'La data di ingresso nella famiglia non è valida.',
        ]);

        $persona = Persona::query()->findOrFail($id);
        $famiglia = Famiglia::query()->findOrFail($request->integer('family_id'));
        $dataEntrata = $request->date('data_entrata')?->toDateString();

        match ($request->string('type')->toString()) {
            'FIGLIO NATO' => $famiglia->assegnaFiglioNato($persona),
            'AGGREGATO' => $famiglia->assegnaAggregato($persona, dataEntrata: $dataEntrata),
            default => $famiglia->assegnaFiglioAccolto($persona, dataEntrata: $dataEntrata),
        };

        return to_route('nomadelfia.person.profile', $persona->id)
            ->with('success', $persona->nominativo.' aggiunto alla famiglia '.$famiglia->nome_famiglia.' con successo');
    }
}
