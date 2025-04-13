<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Http\Request;

final class PersonaPosizioneController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $posattuale = $persona->posizioneAttuale();
        $storico = $persona->posizioniStorico;

        return view('nomadelfia.persone.posizione.show', compact('persona', 'posattuale', 'storico'));
    }

    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'posizione_id' => 'required',
            'data_inizio' => 'required|date',
            // "data_fine" => "date",
        ], [
            'posizione_id.required' => 'La posizione è obbligatorio',
            'data_inizio.required' => 'La data di inizio della posizione è obbligatoria.',
            // 'data_fine.required'=>"La data fine della posizione è obbligatoria.",
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->assegnaPosizione($request->posizione_id, Carbon::parse($request->data_inizio), Carbon::parse($request->data_fine));

        return redirect()
            ->action([self::class, 'index'], ['idPersona' => $persona->id])
            ->withSuccess("Nuova posizione assegnata a $persona->nominativo  con successo.");
    }

    public function update(Request $request, $idPersona, $id)
    {
        $request->validate([
            'current_data_inizio' => 'required',
            'new_data_inizio' => 'required|date',
        ], [
            'new_data_inizio.date' => 'La nuova data di inzio posizione non è una data valida',
            'new_data_inizio.required' => 'La nuova data di inizio della posizione è obbligatoria.',
            'current_data_inizio.required' => 'La data di inizio della posizione è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        if ($persona->modificaDataInizioPosizione($id, Carbon::parse($request->current_data_inizio), Carbon::parse($request->new_data_inizio))) {
            return redirect()
                ->action([self::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("Posizione modificata di $persona->nominativo con successo");
        }

        return redirect()->back()->withError("Impossibile aggiornare la posizione di  $persona->nominativo");
    }

    public function delete($idPersona, $id)
    {
        $persona = Persona::findOrFail($idPersona);
        $res = $persona->posizioni()->detach($id);
        if ($res) {
            return redirect()
                ->action([self::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("Posizione rimossa consuccesso per $persona->nominativo ");
        }

        return redirect()->back()->withErro("Errore. Impossibile rimuovere la posizione per $persona->nominativo");

    }
}
