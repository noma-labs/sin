<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class PersonPositionController
{
    public function index($id)
    {
        $persona = Persona::findOrFail($id);
        $posattuale = $persona->posizioneAttuale();
        $storico = $persona->posizioniStorico;

        return view('nomadelfia.persone.posizione.show', compact('persona', 'posattuale', 'storico'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'posizione_id' => ['required'],
            'data_inizio' => ['required', 'date'],
            // "data_fine" => "date",
        ], [
            'posizione_id.required' => 'La posizione è obbligatorio',
            'data_inizio.required' => 'La data di inizio della posizione è obbligatoria.',
            // 'data_fine.required'=>"La data fine della posizione è obbligatoria.",
        ]);
        $persona = Persona::findOrFail($id);
        $persona->assegnaPosizione($request->posizione_id, \Illuminate\Support\Facades\Date::parse($request->data_inizio), \Illuminate\Support\Facades\Date::parse($request->data_fine));

        return redirect()
            ->action([self::class, 'index'], $persona->id)
            ->withSuccess("Nuova posizione assegnata a $persona->nominativo  con successo.");
    }

    public function update(Request $request, $idPersona, $id)
    {
        $request->validate([
            'current_data_inizio' => ['required'],
            'new_data_inizio' => ['required', 'date'],
        ], [
            'new_data_inizio.date' => 'La nuova data di inzio posizione non è una data valida',
            'new_data_inizio.required' => 'La nuova data di inizio della posizione è obbligatoria.',
            'current_data_inizio.required' => 'La data di inizio della posizione è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        if ($persona->modificaDataInizioPosizione($id, \Illuminate\Support\Facades\Date::parse($request->current_data_inizio), \Illuminate\Support\Facades\Date::parse($request->new_data_inizio))) {
            return redirect()
                ->action([self::class, 'index'], $persona->id)
                ->withSuccess("Posizione modificata di $persona->nominativo con successo");
        }

        return back()->withError("Impossibile aggiornare la posizione di  $persona->nominativo");
    }

    public function delete($id, $idPos)
    {
        $persona = Persona::findOrFail($id);
        $res = $persona->posizioni()->detach($idPos);
        if ($res) {
            return redirect()
                ->action([self::class, 'index'], $persona->id)
                ->withSuccess("Posizione rimossa consuccesso per $persona->nominativo ");
        }

        return back()->withErro("Errore. Impossibile rimuovere la posizione per $persona->nominativo");

    }
}
