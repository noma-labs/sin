<?php

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class GruppifamiliariController extends CoreBaseController
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
        $single = $gruppo->Single();
        $famiglie = $gruppo->Famiglie();

        return view('nomadelfia.gruppifamiliari.edit', compact('gruppo', 'single', 'famiglie'));
    }

    public function assegnaCapogruppo(Request $request, $id)
    {
        $validatedData = $request->validate([
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
