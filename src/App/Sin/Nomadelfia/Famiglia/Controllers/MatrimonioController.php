<?php

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Actions\CreateMarriageAction;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;
use Faker\Provider\Person;
use Illuminate\Http\Request;

class MatrimonioController extends CoreBaseController
{
    public function create()
    {
        $singleMale = Famiglia::notAlreadyMarried()->male()->maggiorenni()->get();
        $singleFemale = Famiglia::notAlreadyMarried()->female()->maggiorenni()->get();
        return view('nomadelfia.famiglie.create', compact('singleMale', 'singleFemale'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'husband' => 'required',
            'wife' => 'required',
            'data_matrimonio' => 'required|date',
        ], [
            'husband.required' => 'Il nome dello sposo è obbligatorio',
            'wife.unique' => 'Il nome della sposa è obbligatorio',
            'data_matrimonio.required' => 'La data di matrimonio è obbligatoria.',
        ]);
        $husband = Persona::findOrFail($request->husband);
        $wife = Persona::findOrFail($request->wife);
        $act = app(CreateMarriageAction::class);
        $fam = $act->execute($husband, $wife, Carbon::parse($request->data_matrimonio));

        return redirect(route('nomadelfia.famiglia.dettaglio',
            ['id' => $fam->id]))->withSuccess("Matrionio creato con successo");
    }
}
