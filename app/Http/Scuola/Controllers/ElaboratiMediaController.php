<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\Models\Elaborato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

final class ElaboratiMediaController
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
        $destinationPath = "elaborati/{$as->endYear}_{$elaborato->collocazione}.{$file->getClientOriginalExtension()}";

        // Store file and check if successful
        $stored = Storage::disk('media_originals')->put($destinationPath, file_get_contents($file->getRealPath()));

        if ($stored) {
            // Store cover image as PNG in media_previews/elaborati/YYYY-COLLOCAZIONE.png if present
            if ($request->hasFile('cover_image')) {
                $cover = $request->file('cover_image');
                $coverPreviewPath = "elaborati/{$as->endYear}_{$elaborato->collocazione}.png";
                // Convert to PNG if not already (optional: use Intervention Image or similar)
                if ($cover->getClientOriginalExtension() !== 'png') {
                    $image = Image::make($cover)->encode('png');
                    Storage::disk('media_previews')->put($coverPreviewPath, $image);
                } else {
                    Storage::disk('media_previews')->put($coverPreviewPath, file_get_contents($cover->getRealPath()));
                }
                // Optionally update DB
                $elaborato->cover_image_path = $coverPreviewPath;
                $elaborato->save();
            }

            // Update the elaborato record with the file details
            $elaborato->update([
                'file_path' => $destinationPath,
                'file_mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'file_hash' => hash_file('sha256', $file->getPathname()),
            ]);

            return redirect()->back()->withSuccess('Caricato correttameente');
        }

        return redirect()->back()->withError('Errore nel salvataggio del file.');

    }
}
