<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Controllers;

use App\Anagrafe\Famiglia\Actions\CreateMarriageAction;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
final class MarriageController
{
    #[Middleware('can:popolazione.persona.inserisci')]
    public function create(Request $request)
    {
        $persona = $request->filled('persona') ? Persona::query()->find($request->integer('persona')) : null;
        $husbandId = $this->selectedSpouse($request->old('husband'), $persona?->isMaschio() ? $persona->id : null);
        $wifeId = $this->selectedSpouse($request->old('wife'), $persona && ! $persona->isMaschio() ? $persona->id : null);

        return view('anagrafe.famiglie.create', compact('husbandId', 'wifeId'));
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request)
    {
        $request->validate([
            'husband' => ['bail', 'required', 'integer', $this->canMarry('M', 'Lo sposo deve essere un uomo maggiorenne, vivo e non già sposato.')],
            'wife' => ['bail', 'required', 'integer', $this->canMarry('F', 'La sposa deve essere una donna maggiorenne, viva e non già sposata.')],
            'data_matrimonio' => ['required', 'date'],
        ], [
            'husband.required' => 'Il nome dello sposo è obbligatorio',
            'wife.required' => 'Il nome della sposa è obbligatorio',
            'data_matrimonio.required' => 'La data di matrimonio è obbligatoria.',
        ]);
        $husband = Persona::findOrFail($request->husband);
        $wife = Persona::findOrFail($request->wife);
        $act = resolve(CreateMarriageAction::class);
        $fam = $act->execute($husband, $wife, \Illuminate\Support\Facades\Date::parse($request->data_matrimonio));

        return redirect(route('anagrafe.families.show',
            ['id' => $fam->id]))->withSuccess('Matrionio creato con successo');
    }

    /**
     * Returns the spouse to preselect: the one submitted before a validation error, otherwise the person the matrimonio starts from.
     *
     * @return list<int>
     */
    private function selectedSpouse(mixed $oldInput, ?int $fromPersona): array
    {
        if (is_numeric($oldInput)) {
            return [(int) $oldInput];
        }

        return $fromPersona !== null ? [$fromPersona] : [];
    }

    /**
     * Validates that the person has the given sex, is an adult, is alive and is not already married.
     */
    private function canMarry(string $sesso, string $message): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($sesso, $message): void {
            $isCandidate = Famiglia::notAlreadyMarried()
                ->maggiorenni()
                ->where('persone.sesso', $sesso)
                ->where('persone.id', $value)
                ->exists();

            if (! $isCandidate) {
                $fail($message);
            }
        };
    }
}
