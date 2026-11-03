<?php

declare(strict_types=1);

namespace App\Anagrafe\Persona\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The photo of a person shown in the detail pages. The photos are kept in the private "avatars" disk
 * and served through this controller, so only the users allowed to see the persone can see them.
 */
#[Middleware('auth')]
final class PersonAvatarController
{
    private const DISK = 'avatars';

    #[Middleware('can:popolazione.persona.visualizza')]
    public function show(string $id): StreamedResponse
    {
        $persona = Persona::query()->findOrFail($id);

        $path = $persona->avatar_path;

        if ($path === null || ! $this->disk()->exists($path)) {
            abort(404);
        }

        return $this->disk()->response($path);
    }

    #[Middleware('can:popolazione.persona.modifica')]
    public function store(Request $request, string $id): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'avatar.required' => 'Selezionare una foto.',
            'avatar.image' => 'Il file deve essere una immagine.',
            'avatar.mimes' => 'La foto deve essere in formato JPG, PNG o WEBP.',
            'avatar.max' => 'La foto non può superare i 5 MB.',
        ]);

        $previousPath = $persona->avatar_path;
        /** @var UploadedFile $file */
        $file = $request->file('avatar');
        $path = $file->storeAs('', "{$persona->id}.{$file->extension()}", self::DISK);

        if ($path === false) {
            return back()->with('error', "Errore durante il caricamento della foto di $persona->nominativo.");
        }

        if ($previousPath !== null && $previousPath !== $path) {
            $this->disk()->delete($previousPath);
        }

        $persona->avatar_path = $path;
        $persona->save();

        return back()->with('success', "Foto di $persona->nominativo aggiornata correttamente.");
    }

    #[Middleware('can:popolazione.persona.modifica')]
    public function destroy(string $id): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);

        if ($persona->avatar_path !== null) {
            $this->disk()->delete($persona->avatar_path);
            $persona->avatar_path = null;
            $persona->save();
        }

        return back()->with('success', "Foto di $persona->nominativo eliminata correttamente.");
    }

    private function disk(): FilesystemAdapter
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(self::DISK);

        return $disk;
    }
}
