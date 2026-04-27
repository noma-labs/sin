<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Nomadelfia\Famiglia\Actions\CreateMarriageAction;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
final class MarriageController
{
    #[Middleware('can:popolazione.persona.inserisci')]
    public function create()
    {
        $singleMale = Famiglia::notAlreadyMarried()->male()->maggiorenni()->get();
        $singleFemale = Famiglia::notAlreadyMarried()->female()->maggiorenni()->get();

        return view('nomadelfia.famiglie.create', compact('singleMale', 'singleFemale'));
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request)
    {
        $request->validate([
            'husband' => ['required'],
            'wife' => ['required'],
            'data_matrimonio' => ['required', 'date'],
        ], [
            'husband.required' => 'Il nome dello sposo è obbligatorio',
            'wife.unique' => 'Il nome della sposa è obbligatorio',
            'data_matrimonio.required' => 'La data di matrimonio è obbligatoria.',
        ]);
        $husband = Persona::findOrFail($request->husband);
        $wife = Persona::findOrFail($request->wife);
        $act = resolve(CreateMarriageAction::class);
        $fam = $act->execute($husband, $wife, \Illuminate\Support\Facades\Date::parse($request->data_matrimonio));

        return redirect(route('nomadelfia.families.show',
            ['id' => $fam->id]))->withSuccess('Matrionio creato con successo');
    }
}
