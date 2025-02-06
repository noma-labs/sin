<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class CoverImageController
{
    public function create($id)
    {
        $elaborato = Elaborato::findOrFail($id);

        return view('scuola.elaborati.cover.create', compact('elaborato'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|image|mimes:png|max:1048576', // 10MB max size
        ]);

        $elaborato = Elaborato::findOrFail($id);

        $file = $request->file('file');
        $pathToImage = $file->getPathname();

        $thumbWidth = 150; // Set the desired thumbnail width
        $newImage = $this->scaleImage($pathToImage, $thumbWidth);

        $tempThumbnailPath = sys_get_temp_dir().'/cover-'.$id.'.png';
        imagepng($newImage, $tempThumbnailPath);

        $thumbFileName = pathinfo($elaborato->file_path, PATHINFO_FILENAME);
        $thumbFolder = pathinfo($elaborato->file_path, PATHINFO_DIRNAME);

        $thumbFileName = $thumbFolder.'/'.$thumbFileName.'-cover.png';

        Storage::disk('public')->put($thumbFileName, file_get_contents($tempThumbnailPath));

        $elaborato->cover_image_path = $thumbFileName;
        $elaborato->save();

        imagedestroy($newImage);

        return redirect()->route('scuola.elaborati.show', $id);
    }

    private function scaleImage($pathToImages, $thumbWidth)
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
