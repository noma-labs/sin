<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Persona\Actions\SalvaAvatarPersonaAction;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The avatar of a persona, a square photo of the face kept in the private media_previews disk: it is served through
 * this controller, so only the users allowed to see the persone can see it, and uploaded from the profile, to add
 * the photo of a persona without one or to replace it.
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

    /**
     * Saves the uploaded photo as the avatar, cut in a square on the face as the import does (SalvaAvatarPersonaAction),
     * replacing the one the persona has.
     */
    #[Middleware('can:popolazione.persona.modifica')]
    public function store(Request $request, string $id, SalvaAvatarPersonaAction $salvaAvatar): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'avatar.required' => 'Scegli una foto.',
            'avatar.image' => 'Il file deve essere una foto.',
            'avatar.mimes' => 'La foto deve essere in formato JPG, PNG o WEBP.',
            'avatar.max' => 'La foto non può superare i 10 MB.',
        ]);

        /** @var UploadedFile $foto */
        $foto = $request->file('avatar');
        try {
            $salvaAvatar->execute($persona, $foto->getPathname());
        } catch (RuntimeException) {
            return back()->withErrors(['avatar' => 'La foto non si può leggere: prova con un altro file.']);
        }

        return back()->with('success', "Foto di {$persona->nome} {$persona->cognome} salvata.");
    }
}
