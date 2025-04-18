<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use Illuminate\Http\Request;

final class FamilyController
{
    public function index()
    {
        $capifamiglieMaschio = Famiglia::onlyCapoFamiglia()->maschio();
        $capifamiglieFemmina = Famiglia::onlyCapoFamiglia()->femmina();

        $singleMaschio = Famiglia::single()->maschio();
        $singleFemmine = Famiglia::single()->femmina();

        $famigliaError = Famiglia::famigliaConErrore();

        return view('nomadelfia.famiglie.index',
            compact('capifamiglieMaschio', 'capifamiglieFemmina', 'singleMaschio', 'singleFemmine', 'famigliaError'));
    }

    public function show(Request $request, $id)
    {
        $famiglia = Famiglia::findorfail($id);
        $componenti = $famiglia->mycomponenti();
        $gruppoAttuale = $famiglia->gruppoFamiliareAttuale();
        $gruppiStorici = $famiglia->gruppiFamiliariStorico();

        return view('nomadelfia.famiglie.show', compact('famiglia', 'componenti', 'gruppoAttuale', 'gruppiStorici'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome_famiglia' => 'required',
            'data_creazione' => 'required|date',
        ], [
            'nome_famiglia.required' => 'Il nome della fmaiglia è obbligatorio.',
            'data_creazione.required' => 'La data di creazione della famiglia è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);

        $famiglia->nome_famiglia = $request->nome_famiglia;
        $famiglia->data_creazione = $request->data_creazione;
        $saved = $famiglia->save();
        if ($saved) {
            return redirect(route('nomadelfia.families.show',
                ['id' => $id]))->withSuccess("Famiglia $famiglia->nome_famiglia aggiornata con successo");
        }

        return redirect(route('nomadelfia.families.show',
            ['id' => $id]))->withErrors("Errore. Famiglia $famiglia->nome_famiglia non aggioranta");

    }
}
