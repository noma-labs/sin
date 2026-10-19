<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Controllers;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
final class FamilyController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function index(Request $request)
    {
        $interni = Persona::idsInterni();
        $tuttiSingle = $request->query('single') === 'tutti';

        $capifamiglieMaschio = Famiglia::onlyCapoFamiglia()->maschio();
        $capifamiglieFemmina = Famiglia::onlyCapoFamiglia()->femmina();

        $singleMaschio = Famiglia::single()->maschio()
            ->when(! $tuttiSingle, fn ($query) => $query->whereIn('persone.id', $interni));
        $singleFemmine = Famiglia::single()->femmina()
            ->when(! $tuttiSingle, fn ($query) => $query->whereIn('persone.id', $interni));

        $famigliaError = Famiglia::famigliaConErrore();

        return view('anagrafe.famiglie.index',
            compact('capifamiglieMaschio', 'capifamiglieFemmina', 'singleMaschio', 'singleFemmine', 'famigliaError', 'interni', 'tuttiSingle'));
    }

    #[Middleware('can:popolazione.persona.visualizza')]
    public function show(Request $request, $id)
    {
        $famiglia = Famiglia::findorfail($id);
        $componenti = $famiglia->mycomponenti();
        $gruppoAttuale = $famiglia->gruppoFamiliareAttuale();
        $gruppiStorici = $famiglia->gruppiFamiliariStorico();

        return view('anagrafe.famiglie.show', compact('famiglia', 'componenti', 'gruppoAttuale', 'gruppiStorici'));
    }

    #[Middleware('can:popolazione.persona.modifica')]
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome_famiglia' => ['required'],
            'data_creazione' => ['required', 'date'],
        ], [
            'nome_famiglia.required' => 'Il nome della fmaiglia è obbligatorio.',
            'data_creazione.required' => 'La data di creazione della famiglia è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);

        $famiglia->nome_famiglia = $request->nome_famiglia;
        $famiglia->data_creazione = $request->data_creazione;
        $saved = $famiglia->save();
        if ($saved) {
            return redirect(route('anagrafe.families.show',
                ['id' => $id]))->withSuccess("Famiglia $famiglia->nome_famiglia aggiornata con successo");
        }

        return redirect(route('anagrafe.families.show',
            ['id' => $id]))->withErrors("Errore. Famiglia $famiglia->nome_famiglia non aggioranta");

    }
}
