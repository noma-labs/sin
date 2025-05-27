<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
use GdImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class CoverImageController
{
    public function create(int $id)
    {
        $elaborato = Elaborato::query()->findOrFail($id);

        return view('scuola.elaborati.cover.create', compact('elaborato'));
    }

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

    public function store(Request $request, int $id)
    {
        $elaborato = Elaborato::query()->findOrFail($id);

        $request->validate([
            'file' => 'required|image|mimes:png|max:1048576', // 10MB max size
        ]);

        if ($elaborato->collocazione === null){
            return redirect()->back()->with('error', 'Elaborato deve avere una collocazione.');
        }

        $file = $request->file('file');
        $pathToImage = $file->getPathname();

        $thumbWidth = 150; // Set the desired thumbnail width
        $newImage = $this->scaleImage($pathToImage, $thumbWidth);

        $tempThumbnailPath = sys_get_temp_dir().'/cover-'.$id.'.png';
        imagepng($newImage, $tempThumbnailPath);

        $coverDestinationPath = "elaborati/{$elaborato->collocazione }.png";

        Storage::disk('media_previews')->put($coverDestinationPath, file_get_contents($tempThumbnailPath));

        $elaborato->cover_image_path = $coverDestinationPath;
        $elaborato->save();

        imagedestroy($newImage);

        return redirect()->route('scuola.elaborati.show', $id);
    }

    private function scaleImage($pathToImages, int $thumbWidth): GdImage|false
    {
        $img = imagecreatefrompng($pathToImages);
        $sourceWidth = imagesx($img);
        $sourceHeight = imagesy($img);

        $desiredWidth = $thumbWidth;
        $desiredHeight = (int) floor($sourceHeight * ($thumbWidth / $sourceWidth));

        $newImage = imagecreatetruecolor($desiredWidth, $desiredHeight);

        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        imagecopyresampled(
            $newImage,
            $img,
            0,
            0,
            0,
            0,
            $desiredWidth,
            $desiredHeight,
            $sourceWidth,
            $sourceHeight,
        );
        // Free up memory
        imagedestroy($img);

        return $newImage;
    }
}
