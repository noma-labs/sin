<?php

declare(strict_types=1);

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class GruppifamiliariController
{
    public function view()
    {
        $g = GruppoFamiliare::countComponenti();

        return view('nomadelfia.gruppifamiliari.index', compact('g'));
    }

    public function show($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        return view('nomadelfia.gruppifamiliari.show', compact('persona'));
    }

    public function edit($id)
    {
        $gruppo = GruppoFamiliare::findOrFail($id);
        $single = GruppoFamiliare::single($gruppo)->get();

        $fams = GruppoFamiliare::families($gruppo)->get();
        $famiglie = collect($fams)->groupBy('famiglia_id');

        return view('nomadelfia.gruppifamiliari.edit', compact('gruppo', 'single', 'famiglie'));
    }

    public function assegnaCapogruppo(Request $request, $id)
    {
        $request->validate([
            'nuovo' => 'required',
            'inizio' => 'required',
        ], [
            'nuovo.required' => 'Il nuovo capogruppo è abbligatoripo',
            'inizio.required' => 'La data di inizio è obbligatoria',
        ]);
        $gruppo = GruppoFamiliare::findOrFail($id);
        $gruppo->assegnaCapogruppo($request->nuovo, $request->inizio);

        return redirect()->back()->withSuccess('NUovo capogruppo inserito con successo');
    }
}
