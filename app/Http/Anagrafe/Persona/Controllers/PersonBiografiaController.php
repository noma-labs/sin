<?php

declare(strict_types=1);

namespace App\Anagrafe\Persona\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

/**
 * The biografia of a person, edited on its own from the header of the profile.
 */
#[Middleware('auth')]
final class PersonBiografiaController
{
    #[Middleware('can:popolazione.persona.modifica')]
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'biografia' => ['nullable', 'string', 'max:65535'],
        ]);

        $persona = Persona::query()->findOrFail($id);
        $persona->biografia = $validated['biografia'] ?? null;
        $persona->save();

        return to_route('nomadelfia.person.profile', $persona->id)
            ->with('success', "Biografia di $persona->nominativo aggiornata correttamente.");
    }
}
