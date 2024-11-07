<?php

namespace App\Nomadelfia\Persona\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaAnagraficaController
{
    public function edit($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        return view('nomadelfia.persone.edit_anagrafica', compact('persona'));
    }

    public function update(Request $request, $idPersona)
    {
        $request->validate([
            'nome' => 'required',
            'cognome' => 'required',
            'datanascita' => 'required',
            //            "luogonascita" => "required",
            'sesso' => 'required',
        ], [
            'nome.required' => 'Il nome è obbligatorie',
            'cognome.required' => 'Il cognome è obbligatorio',
            'datanascita.required' => 'La data di nascita è obbligatoria',
            //            "luogonascita.required" => "Il luogo di nascita è obbligatorio",
            'sesso.required' => 'Il sesso è obbligatorio',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->nome = $request->nome;
        $persona->cognome = $request->cognome;
        $persona->data_nascita = $request->datanascita;
        $persona->cf = $request->codicefiscale;
        $persona->provincia_nascita = $request->luogonascita;
        $persona->sesso = $request->sesso;
        $persona->biografia = $request->get('biografia', $persona->biografia);
        $persona->data_decesso = $request->get('data_decesso', $persona->data_decesso);
        if ($persona->save()) {
            return redirect()->route('nomadelfia.persone.dettaglio',
                ['idPersona' => $idPersona])->withSuccess("Dati anagrafici di $persona->nominativo aggiornati correttamente. ");
        } else {
            return redirect()->route('nomadelfia.persone.dettaglio',
                ['idPersona' => $idPersona])->withError("Errore dureante l'aggiornamente dei dati anagrafici di $persona->nominativo.");
        }
    }

    public function create()
    {
        return view('nomadelfia.persone.inserimento.dati_anagrafici');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominativo' => 'required',
            'nome' => 'required',
            'cognome' => 'required',
            'data_nascita' => 'required|date',
            'luogo_nascita' => 'required',
            'sesso' => 'required',
        ], [
            'nominativo.required' => 'Il nominativo è obbligatorio',
            'nominativo.unique' => 'IL nominativo inserito esiste già.',
            'nome.required' => 'Il nome è obbligatorie',
            'cognome.required' => 'Il cognome è obbligatorio',
            'data_nascita.required' => 'La data di nascita è obbligatoria',
            'luogo_nascita.required' => 'IL luogo di nascita è obbligatorio',
            'sesso.required' => 'Il sesso della persona è obbligatorio',
        ]);
        //        $existing  = PopolazioneNomadelfia::presente()->where('nominativo', "", $request->input('nominativo'));
        //        if ($existing->count() > 0) {
        //            return redirect(route('nomadelfia.persone.inserimento'))->withError("Il nominativo inserito è già assegnato alla persona  $existing->nome $existing->cognome ($existing->data_nascita). Usare un altro nominativo.");
        //        }

        $persona = Persona::create(
            [
                'nominativo' => $request->input('nominativo'),
                'sesso' => $request->input('sesso'),
                'nome' => $request->input('nome'),
                'cognome' => $request->input('cognome'),
                'provincia_nascita' => $request->input('luogo_nascita'),
                'data_nascita' => $request->input('data_nascita'),
                'id_arch_pietro' => 0,
            ]
        );
        $res = $persona->save();
        if ($res) {
            return redirect(route('nomadelfia.persone.inserimento.entrata.scelta',
                ['idPersona' => $persona->id]))->withSuccess("Dati anagrafici di $persona->nominativo inseriti correttamente.");
        } else {
            return redirect(route('nomadelfia.persone.create'))->withError("Errore. Persona $persona->nominativo non inserita.");
        }
    }
}
