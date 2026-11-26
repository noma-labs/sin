<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Persona\Actions\SalvaAvatarPersonaAction;
use App\Anagrafe\Persona\Models\Persona;
use App\Photo\Actions\CodaAvatarAction;
use App\Photo\Actions\VoltiPersonaAction;
use App\Photo\DataTransferObjects\RiquadroVolto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Chooses the avatar of a persona from the photos of the archive: among their tagged faces, or drawing the box around
 * the persona on one of their photos. With coda=1 the page belongs to the queue of the persone without an avatar
 * (CodaAvatarAction) and after the choice it goes on to the next persona. It needs the permission to modify the
 * persona and to see the photos.
 */
#[Middleware('auth')]
#[Middleware('can:popolazione.persona.modifica')]
#[Middleware('can:photo.view')]
final class PersonAvatarVoltiController
{
    /**
     * The side of the thumbnail of a face in the page.
     */
    public const LATO_ANTEPRIMA = 160;

    public function index(Request $request, string $id, VoltiPersonaAction $voltiPersona, CodaAvatarAction $coda): View
    {
        $persona = Persona::query()->findOrFail($id);
        $inCoda = $request->boolean('coda');

        return view('nomadelfia.persone.avatar-volti', [
            'persona' => $persona,
            'volti' => $voltiPersona->execute($persona),
            'fotoSenzaVolto' => $voltiPersona->fotoSenzaVolto($persona),
            'inCoda' => $inCoda,
            'prossima' => $inCoda ? $coda->prossima($persona) : null,
        ]);
    }

    /**
     * The face cut from the original photo, kept on the media_previews disk so that each face is decoded only once.
     */
    public function show(string $id, string $photo, string $indice, VoltiPersonaAction $voltiPersona, SalvaAvatarPersonaAction $salvaAvatar): BinaryFileResponse
    {
        $persona = Persona::query()->findOrFail($id);
        $volto = $voltiPersona->volto($persona, (int) $photo, (int) $indice);
        if ($volto === null) {
            abort(404);
        }

        $anteprime = Storage::disk('media_previews');
        $anteprima = "volti/{$volto->photoId}-{$volto->indice}.jpg";
        if (! $anteprime->exists($anteprima)) {
            $originali = Storage::disk('photos');
            if (! $originali->exists($volto->sourceFile)) {
                abort(404);
            }
            try {
                $anteprime->put($anteprima, $salvaAvatar->jpeg($originali->path($volto->sourceFile), self::LATO_ANTEPRIMA, $volto->riquadro()));
            } catch (RuntimeException) {
                abort(404);
            }
        }

        return response()->file($anteprime->path($anteprima))->setPrivate()->setMaxAge(604800);
    }

    /**
     * Saves the chosen face as the avatar, cut from the original photo: only a face of the persona can be chosen.
     */
    public function store(Request $request, string $id, VoltiPersonaAction $voltiPersona, SalvaAvatarPersonaAction $salvaAvatar, CodaAvatarAction $coda): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);
        $dati = $request->validate([
            'photo_id' => ['required', 'integer'],
            'indice' => ['required', 'integer', 'min:0'],
        ]);

        $volto = $voltiPersona->volto($persona, (int) $dati['photo_id'], (int) $dati['indice']);
        if ($volto === null) {
            return back()->withErrors(['volto' => "Questo volto non è di {$persona->nome} {$persona->cognome}."]);
        }

        return $this->salva($request, $persona, $volto->sourceFile, $volto->riquadro(), $salvaAvatar, $coda, 'volto');
    }

    /**
     * The photo of the persona where to draw the box around them: around the face $indice of the photo when given.
     */
    public function riquadro(Request $request, string $id, string $photo, VoltiPersonaAction $voltiPersona): View
    {
        $persona = Persona::query()->findOrFail($id);
        $foto = $voltiPersona->foto($persona, (int) $photo);
        if ($foto === null) {
            abort(404);
        }

        $iniziale = null;
        $volto = $request->filled('indice') ? $voltiPersona->volto($persona, $foto->id, $request->integer('indice')) : null;
        $larghezza = $foto->image_width ?? (int) ($foto->region_info->AppliedToDimensions['W'] ?? 0);
        $altezza = $foto->image_height ?? (int) ($foto->region_info->AppliedToDimensions['H'] ?? 0);
        if ($volto !== null && $larghezza > 0 && $altezza > 0) {
            $iniziale = $volto->riquadro()->normalizzato($larghezza, $altezza);
        }

        return view('nomadelfia.persone.avatar-riquadro', [
            'persona' => $persona,
            'foto' => $foto,
            'iniziale' => $iniziale,
            'inCoda' => $request->boolean('coda'),
        ]);
    }

    /**
     * Saves the box drawn on a photo of the persona as the avatar. The box is normalized to the photo as the browser
     * shows it, rotated by its EXIF orientation, as SalvaAvatarPersonaAction cuts it.
     */
    public function storeRiquadro(Request $request, string $id, string $photo, VoltiPersonaAction $voltiPersona, SalvaAvatarPersonaAction $salvaAvatar, CodaAvatarAction $coda): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($id);
        $foto = $voltiPersona->foto($persona, (int) $photo);
        if ($foto === null) {
            abort(404);
        }

        $messaggio = 'Disegna il riquadro intorno alla persona.';
        $dati = $request->validate([
            'x' => ['required', 'numeric', 'min:0', 'max:1'],
            'y' => ['required', 'numeric', 'min:0', 'max:1'],
            'w' => ['required', 'numeric', 'gt:0', 'max:1'],
            'h' => ['required', 'numeric', 'gt:0', 'max:1'],
        ], [
            'required' => $messaggio,
            'numeric' => $messaggio,
            'min' => $messaggio,
            'max' => $messaggio,
            'gt' => $messaggio,
        ]);
        [$x, $y, $larghezza, $altezza] = [(float) $dati['x'], (float) $dati['y'], (float) $dati['w'], (float) $dati['h']];
        // a little tolerance for the rounding of the page
        if ($x + $larghezza > 1.001 || $y + $altezza > 1.001) {
            return back()->withErrors(['riquadro' => 'Il riquadro esce dalla foto.']);
        }

        return $this->salva($request, $persona, $foto->source_file, RiquadroVolto::disegnato($x, $y, $larghezza, $altezza), $salvaAvatar, $coda, 'riquadro');
    }

    /**
     * Cuts the avatar from the original photo, then goes back to the profile or, in the queue, on to the next persona.
     *
     * @param  string  $campo  the field of the error when the photo can not be read
     */
    private function salva(Request $request, Persona $persona, string $sourceFile, RiquadroVolto $riquadro, SalvaAvatarPersonaAction $salvaAvatar, CodaAvatarAction $coda, string $campo): RedirectResponse
    {
        // the next one is found before saving, while the persona is still in the queue
        $prossima = $request->boolean('coda') ? $coda->prossima($persona) : null;

        $originali = Storage::disk('photos');
        try {
            if (! $originali->exists($sourceFile)) {
                throw new RuntimeException("Missing photo {$sourceFile}");
            }
            $salvaAvatar->execute($persona, $originali->path($sourceFile), $riquadro);
        } catch (RuntimeException) {
            return back()->withErrors([$campo => 'La foto originale non si trova o non si può leggere.']);
        }

        $salvata = "Foto di {$persona->nome} {$persona->cognome} salvata.";
        if (! $request->boolean('coda')) {
            return to_route('nomadelfia.person.profile', $persona->id)->with('success', $salvata);
        }
        if ($prossima === null) {
            return to_route('nomadelfia.person.senza-avatar')->with('success', "{$salvata} Sei arrivato in fondo alla coda.");
        }

        return to_route('nomadelfia.person.avatar.volti', ['id' => $prossima, 'coda' => 1])->with('success', $salvata);
    }
}
