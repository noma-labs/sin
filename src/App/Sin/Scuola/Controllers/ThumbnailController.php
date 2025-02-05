<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
use Illuminate\Http\Request;

final class ThumbnailController
{

    public function create($id)
    {
        $elaborato = Elaborato::findOrFail($id);


        return view('scuola.elaborati.thumbnail.create',compact('elaborato'));
    }

    public function store(Request $request, $id)
    {
        $elaborato = Elaborato::findOrFail($id);

        $file = $request->file('file');

        $pathToImages = $file->getPathname();
        $fname = $file->getClientOriginalName();

        // Create a thumbnail
        $thumbWidth = 150; // Set the desired thumbnail width
        $thumbnail = $this->createThumbnail($pathToImages, $fname, $thumbWidth);

        $storagePath = $file->storeAs('2026', 'thumbnail-2', 'scuola');

        return response($thumbnail, 200)->header('Content-Type', 'image/png');

    }
    private function createThumbnail($pathToImages, $fname, $thumbWidth)
    {
        // Load the image
        $img = imagecreatefrompng($pathToImages);
        $width = imagesx($img);
        $height = imagesy($img);

        // Calculate thumbnail size
        $new_width = $thumbWidth;
        $new_height = (int) floor($height * ($thumbWidth / $width));

        // Create a new temporary image
        $tmp_img = imagecreatetruecolor($new_width, $new_height);

        // Copy and resize old image into new image
        imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Save the thumbnail into a temporary file
        $thumbnailPath = sys_get_temp_dir() . '/' . $fname . '_thumb.png';
        imagepng($tmp_img, $thumbnailPath);

        // Free up memory
        imagedestroy($img);
        imagedestroy($tmp_img);

        return $thumbnailPath;
    }


}
