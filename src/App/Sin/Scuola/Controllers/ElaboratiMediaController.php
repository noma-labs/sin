<?php

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\Models\Elaborato;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ElaboratiMediaController
{
    public function store(Request $request, $id)
    {
        $elaborato = Elaborato::findOrFail($id);

        if (! $elaborato->collocazione) {
            return redirect()->back()->withError('Elaborato deve avere una collocazione');
        }
        if (! $elaborato->anno_scolastico) {
            return redirect()->back()->withError('Elaborato deve avere anno scolastico.');
        }

        $as = AnnoScolastico::fromString($elaborato->anno_scolastico);

        $file = $request->file('file');
        $titleSlug = Str::slug($elaborato->titolo);
        $filePath = "{$as->endYear}/{$elaborato->collocazione}_{$titleSlug}";
        $fileName = "{$elaborato->collocazione}_{$titleSlug}.{$file->getClientOriginalExtension()}";

        $storagePath = $file->storeAs($filePath, $fileName, 'scuola');
        if (! $storagePath) {
            return redirect()->back()->withError('Errore durante il caricamento del file.');
        }

        // Update the elaborato record with the file details
        $elaborato->update([
            'file_path' => $storagePath,
            'file_mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'file_hash' => hash_file('sha256', $file->getPathname()),
        ]);

        return redirect()->back()->withSuccess('Caricato correttameente');

    }
}