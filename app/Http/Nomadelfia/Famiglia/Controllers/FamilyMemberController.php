<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\Persona\Models\Persona;
use Exception;
use Illuminate\Http\Request;

final class FamilyMemberController
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'persona_id' => 'required',
            'posizione' => 'required',
            'stato' => 'required',
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
            'stato.required' => 'Lo stato della persona è obbligatoria.',
            'posizione.required' => 'La posizione nella famiglia è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        $persona = Persona::findorfail($request->persona_id);

        switch ($request->posizione) {
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
                return redirect(route('nomadelfia.families.show',
                    ['id' => $id]))->withErrors("Posizione `{$request->posizione}` non riconosciuta");
        }

        return redirect(route('nomadelfia.families.show',
            ['id' => $id]))->withSuccess("$persona->nominativo aggiunto alla famiglia $famiglia->nome_famiglia con successo");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'persona_id' => 'required',
            'posizione' => 'required',
            'stato' => 'required',
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

            return redirect(route('nomadelfia.families.show',
                ['id' => $id]))->withSuccess('Componente aggiornato con successo');
        } catch (Exception) {
            return redirect(route('nomadelfia.families.show',
                ['id' => $id]))->withError('Errore. Nessun componente aggiornato alla famiglia.');
        }
    }
}
