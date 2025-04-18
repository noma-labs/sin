<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use Illuminate\Http\Request;

final class FamilyGruppofamiliareController
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'nuovo_gruppo_id' => 'required',
            'data_cambiogruppo' => 'required|date',
        ], [
            'nuovo_gruppo_id.required' => 'Il nuovo gruppo dove spostare la famiglia è obbligatorio',
            'data_cambiogruppo.required' => 'La data del cambio di gruppo è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        $gruppo_corrente = $famiglia->gruppoFamiliareAttualeOrFail();
        $famiglia->assegnaFamigliaANuovoGruppoFamiliare($gruppo_corrente->id, $request->data_cambiogruppo,
            $request->nuovo_gruppo_id, $request->data_cambiogruppo);

        return redirect(route('nomadelfia.families.show', $id))->withSuccess('Famiglia spostata nel gruppo familiare con successo');
    }
}
