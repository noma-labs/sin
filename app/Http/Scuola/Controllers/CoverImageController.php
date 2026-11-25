<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
use GdImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class CoverImageController
{
    public function show(int $id)
    {
        $elaborato = Elaborato::query()->findOrFail($id);

        if (! Storage::disk('media_previews')->exists($elaborato->cover_image_path)) {
            abort(404);
        }

        $fileContent = Storage::disk('media_previews')->get($elaborato->cover_image_path);
        $mimeType = Storage::disk('media_previews')->mimeType($elaborato->cover_image_path) ?? 'image/png';

        return response($fileContent, 200)->header('Content-Type', $mimeType);
    }

    /**
     * Stores a PNG or JPEG (as the scanner produces it) as a 150px wide PNG thumbnail at elaborati/{collocazione}.png.
     */
    public function store(Request $request, int $id): RedirectResponse
    {
        $elaborato = Elaborato::query()->findOrFail($id);

        $request->validate([
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:10240'],
        ], [
            'file.required' => 'Scegli un file per la copertina.',
            'file.image' => "La copertina deve essere un'immagine.",
            'file.mimes' => 'La copertina deve essere in formato JPG o PNG.',
            'file.max' => 'La copertina non può superare i 10 MB.',
        ]);

        if ($elaborato->collocazione === null) {
            return back()->with('error', "Assegna una collocazione all'elaborato prima di caricare la copertina.");
        }

        // a high resolution scan of an A4 page takes more than 100 MB once decoded
        ini_set('memory_limit', '1024M');

        $file = $request->file('file');
        $contenuto = $file instanceof UploadedFile ? @file_get_contents($file->getPathname()) : false;
        $image = $contenuto === false ? false : @imagecreatefromstring($contenuto);
        if (! $image instanceof GdImage) {
            throw ValidationException::withMessages(['file' => 'La copertina non si può leggere: prova con un altro file.']);
        }

        $thumbWidth = 150;
        $thumbnail = $this->scaleImage($image, $thumbWidth);

        ob_start();
        imagepng($thumbnail);
        $png = (string) ob_get_clean();

        $coverDestinationPath = "elaborati/{$elaborato->collocazione}.png";
        Storage::disk('media_previews')->put($coverDestinationPath, $png);

        $elaborato->cover_image_path = $coverDestinationPath;
        $elaborato->save();

        return to_route('scuola.elaborati.show', $id)->with('success', 'Copertina salvata.');
    }

    /**
     * @param  int<1, max>  $thumbWidth
     */
    private function scaleImage(GdImage $image, int $thumbWidth): GdImage
    {
        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);

        $desiredHeight = max(1, (int) floor($sourceHeight * ($thumbWidth / $sourceWidth)));

        $newImage = imagecreatetruecolor($thumbWidth, $desiredHeight);

        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        imagecopyresampled(
            $newImage,
            $image,
            0,
            0,
            0,
            0,
            $thumbWidth,
            $desiredHeight,
            $sourceWidth,
            $sourceHeight,
        );

        return $newImage;
    }
}
