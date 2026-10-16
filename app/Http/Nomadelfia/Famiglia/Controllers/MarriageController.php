<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Nomadelfia\Famiglia\Actions\CreateMarriageAction;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Date;

#[Middleware('auth')]
final class MarriageController
{
    #[Middleware('can:popolazione.persona.inserisci')]
    public function create(Request $request): View
    {
        $husbandId = [];
        $wifeId = [];

        if ($request->filled('persona_id')) {
            $persona = Persona::query()->findOrFail($request->integer('persona_id'));
            if ($persona->sesso === 'M') {
                $husbandId = [$persona->id];
            } else {
                $wifeId = [$persona->id];
            }
        }

        return view('nomadelfia.famiglie.create', ['husbandId' => $husbandId, 'wifeId' => $wifeId]);
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request, CreateMarriageAction $action): RedirectResponse
    {
        $request->validate([
            'husband' => ['required', 'exists:db_nomadelfia.persone,id'],
            'wife' => ['required', 'exists:db_nomadelfia.persone,id', 'different:husband'],
            'data_matrimonio' => ['required', 'date'],
        ], [
            'husband.required' => 'Il nome dello sposo è obbligatorio.',
            'husband.exists' => 'Lo sposo selezionato non esiste.',
            'wife.required' => 'Il nome della sposa è obbligatorio.',
            'wife.exists' => 'La sposa selezionata non esiste.',
            'wife.different' => 'Lo sposo e la sposa devono essere persone diverse.',
            'data_matrimonio.required' => 'La data di matrimonio è obbligatoria.',
            'data_matrimonio.date' => 'La data di matrimonio non è valida.',
        ]);

        $husband = Persona::query()->findOrFail($request->integer('husband'));
        $wife = Persona::query()->findOrFail($request->integer('wife'));

        $errors = [
            ...$this->spouseErrors($husband, 'M', 'Lo sposo'),
            ...$this->spouseErrors($wife, 'F', 'La sposa'),
        ];
        if ($errors !== []) {
            return back()->withErrors($errors)->withInput();
        }

        $fam = $action->execute($husband, $wife, Date::parse($request->string('data_matrimonio')->toString()));

        return to_route('nomadelfia.families.show', ['id' => $fam->id])
            ->with('success', 'Matrimonio creato con successo');
    }

    /**
     * @return list<string>
     */
    private function spouseErrors(Persona $persona, string $sesso, string $label): array
    {
        $errors = [];

        if ($persona->sesso !== $sesso) {
            $errors[] = $sesso === 'M'
                ? "{$label} {$persona->nominativo} deve essere un uomo."
                : "{$label} {$persona->nominativo} deve essere una donna.";
        }
        if (! $persona->isMaggiorenne()) {
            $errors[] = "{$label} {$persona->nominativo} deve essere maggiorenne.";
        }
        if ($persona->famigliaPosizione(Famiglia::getCapoFamigliaEnum()) || $persona->famigliaPosizione(Famiglia::getMoglieEnum())) {
            $errors[] = "{$label} {$persona->nominativo} è già sposato/a in una famiglia attiva.";
        }

        return $errors;
    }
}
