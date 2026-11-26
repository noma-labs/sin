<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Photo\Actions\CodaAvatarAction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

/**
 * The persone who are in the photos of the archive, in two tabs: "Da fare", the queue of the ones without an avatar,
 * from which the page with their faces (PersonAvatarVoltiController) goes on to the next one; "Fatte", the avatars
 * already saved, the last ones first, to check them and change them.
 */
#[Middleware('auth')]
#[Middleware('can:popolazione.persona.modifica')]
#[Middleware('can:photo.view')]
final class PersoneSenzaAvatarController
{
    public function index(Request $request, CodaAvatarAction $coda): View
    {
        $fatte = $request->query('stato') === 'fatte';

        return view('nomadelfia.persone.senza-avatar', [
            'fatte' => $fatte,
            'persone' => ($fatte ? $coda->fatte() : $coda->daFare())->paginate($fatte ? 48 : 50)->withQueryString(),
            'numeroDaFare' => $coda->daFare()->count(),
            'numeroFatte' => $coda->fatte()->count(),
        ]);
    }
}
