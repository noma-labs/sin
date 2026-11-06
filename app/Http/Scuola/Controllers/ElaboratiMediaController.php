<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\Models\Elaborato;
use App\Scuola\Traits\StoresElaboratoFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class ElaboratiMediaController
{
    use StoresElaboratoFile;

    public function store(Request $request, $id)
    {
        $request->validate([
            'file' => ['required', 'file'],
        ], [
            'file.required' => 'Il file è obbligatorio.',
        ]);

        $elaborato = Elaborato::findOrFail($id);

        if (! $elaborato->collocazione) {
            return back()->withError('Elaborato deve avere una collocazione');
        }
        if (! $elaborato->anno_scolastico) {
            return back()->withError('Elaborato deve avere anno scolastico.');
        }

        $as = AnnoScolastico::fromString($elaborato->anno_scolastico);

        $file = $request->file('file');
        $destinationPath = $this->storeElaboratoFile(
            $file,
            $as,
            $elaborato->collocazione,
            $elaborato->titolo
        );
        // Update the elaborato record with the file details
        $elaborato->update([
            'file_path' => $destinationPath,
            'file_mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'file_hash' => hash_file('sha256', $file->getPathname()),
        ]);

        return back()->withSuccess('Caricato correttameente');

    }

    public function destroy(int $id): RedirectResponse
    {
        $elaborato = Elaborato::query()->findOrFail($id);

        if (empty($elaborato->file_path)) {
            return back()->with('error', 'Elaborato non ha un file da eliminare.');
        }

        if (! Storage::disk('media_originals')->delete($elaborato->file_path)) {
            return back()->with('error', 'Impossibile eliminare il file dal disco.');
        }

        $elaborato->update([
            'file_path' => null,
            'file_mime_type' => null,
            'file_size' => null,
            'file_hash' => null,
        ]);

        return to_route('scuola.elaborati.show', $elaborato->id)->with('success', 'File eliminato correttamente.');
    }
}
