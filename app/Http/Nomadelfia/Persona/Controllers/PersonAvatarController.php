<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The avatar of a persona, a square photo of the face kept in the private media_previews disk: it is served through
 * this controller, so only the users allowed to see the persone can see it.
 */
#[Middleware('auth')]
final class PersonAvatarController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function show(string $id): BinaryFileResponse
    {
        $persona = Persona::query()->findOrFail($id);
        $disco = Storage::disk('media_previews');

        if ($persona->avatar_path === null || ! $disco->exists($persona->avatar_path)) {
            abort(404);
        }

        // the URL changes when the avatar is replaced (Persona::avatarUrl), so the browser can keep it for a week;
        // private, so a shared cache does not keep it: response()->file() would make it public
        return response()->file($disco->path($persona->avatar_path))->setPrivate()->setMaxAge(604800);
    }
}
