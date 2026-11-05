<?php

declare(strict_types=1);

namespace App\Anagrafe\Exceptions;

use App\Anagrafe\Persona\Models\Persona;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The persona is in the nucleo (stato '1') of more than one famiglia.
 * The user is sent to the famiglie of the persona to choose the one that stays the famiglia attuale.
 */
final class PersonaHasMultipleFamigliaAttuale extends Exception
{
    public function __construct(public readonly Persona $persona)
    {
        parent::__construct("La persona `{$persona->nominativo}` ha più di uno stato familiare attuale.");
    }

    public static function of(Persona $persona): self
    {
        return new self($persona);
    }

    public function render(Request $request): Response|RedirectResponse
    {
        if ($request->expectsJson() || $request->routeIs('anagrafe.person.families')) {
            return response()->view('errors.sin-error', ['exception' => $this], 500);
        }

        return to_route('anagrafe.person.families', $this->persona->id)
            ->withErrors(['famiglia_id' => $this->getMessage().' Scegli la famiglia attuale: le altre passano nello storico.']);
    }
}
