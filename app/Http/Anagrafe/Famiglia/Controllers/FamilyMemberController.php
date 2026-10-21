<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Controllers;

use App\Anagrafe\Exceptions\AnagrafeException;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
final class FamilyMemberController
{
    #[Middleware('can:popolazione.persona.modifica')]
    public function store(Request $request, $id)
    {
        $request->validate([
            'persona_id' => ['required'],
            'posizione' => ['required'],
            'stato' => ['required', 'in:0,1'],
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
            'stato.required' => 'Lo stato della persona è obbligatoria.',
            'posizione.required' => 'La posizione nella famiglia è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        $persona = Persona::findorfail($request->persona_id);
        $stato = $request->string('stato')->toString();

        try {
            switch ($request->posizione) {
                case 'CAPO FAMIGLIA':
                    $famiglia->assegnaCapoFamiglia($persona, stato: $stato);
                    break;
                case 'MOGLIE':
                    $famiglia->assegnaMoglie($persona, $stato);
                    break;
                case 'FIGLIO NATO':
                    $famiglia->assegnaFiglioNato($persona, stato: $stato);
                    break;
                case 'FIGLIO ACCOLTO':
                    $famiglia->assegnaFiglioAccolto($persona, stato: $stato);
                    break;
                default:
                    return redirect(route('anagrafe.families.show',
                        ['id' => $id]))->withErrors("Posizione `{$request->posizione}` non riconosciuta");
            }
        } catch (AnagrafeException $e) {
            return redirect(route('anagrafe.families.show', ['id' => $id]))->withErrors(['persona_id' => $e->getMessage()]);
        }

        return redirect(route('anagrafe.families.show',
            ['id' => $id]))->withSuccess("$persona->nominativo aggiunto alla famiglia $famiglia->nome_famiglia con successo");
    }

    #[Middleware('can:popolazione.persona.modifica')]
    public function update(Request $request, $id)
    {
        $request->validate([
            'persona_id' => ['required'],
            'posizione' => ['required'],
            'stato' => ['required'],
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
            'stato.required' => 'Lo stato della persona è obbligatoria.',
            'posizione.required' => 'La posizione nella famiglia è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        try {
            $famiglia->componenti()->updateExistingPivot($request->persona_id, [
                'stato' => $request->stato,
                'posizione_famiglia' => $request->posizione,
                'note' => $request->note,
            ]);

            return redirect(route('anagrafe.families.show',
                ['id' => $id]))->withSuccess('Componente aggiornato con successo');
        } catch (Exception) {
            return redirect(route('anagrafe.families.show',
                ['id' => $id]))->withError('Errore. Nessun componente aggiornato alla famiglia.');
        }
    }
}
