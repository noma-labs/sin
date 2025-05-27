<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\Models\Elaborato;
use App\Scuola\Traits\StoresElaboratoFile;
use Illuminate\Http\Request;

final class ElaboratiMediaController
{
    use StoresElaboratoFile;

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

        return redirect()->back()->withSuccess('Caricato correttameente');

    }
}
