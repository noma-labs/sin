<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use App\Photo\Models\PhotoEnrico;
use Illuminate\Http\Request;

final class PhotoController
{
    public function index(Request $request)
    {
        $filterYear = $request->input('year');

        $enrico = PhotoEnrico::orderBy('data');
        // ->where(function ($query) {
        //     $query->where('argomento', 'like', '%sport')
        //         ->orWhere('descrizione', 'like', '%sport');
        // });
        if ($filterYear !== null) {
            $enrico = $enrico->whereRaw('YEAR(data)= ?', [$filterYear]);
        }
        $enrico = $enrico->get();

        $photos = Photo::orderBy('taken_at')
            ->where('favorite', 1)
            ->orderBy('taken_at');
        if ($filterYear !== null) {
            $photos = $photos->whereRaw('YEAR(taken_at)= ?', [$filterYear]);
        }
        $photos = $photos->get();

        $years = Photo::selectRaw('YEAR(taken_at) as year, count(*) as `count` ')
            ->groupByRaw('YEAR(taken_at)')
            ->orderByRaw('YEAR(taken_at)')
            ->get();

        return view('photo.index', compact('photos', 'years', 'enrico'));
    }

    public function update(Request $request, string $sha)
    {
        $request->validate([
            'taken_at' => 'required|date',
        ]);

        $photo = Photo::where('sha', $sha)->firstOrFail();
        $photo->taken_at = $request->input('taken_at');
        $photo->save();

        return redirect()->route('photos.index')->with('success', 'Photo updated successfully.');
    }

    public function show(Request $request, string $sha)
    {
        $photo = Photo::where('sha', $sha)->firstOrFail();

        $filepath = storage_path("app/public/foto-sport/$photo->folder_title/$photo->file_name");

        $image = imagecreatefromjpeg($filepath);
        if (! $image) {
            exit('Failed to load image.');
        }

        $xmpData = json_decode($photo->region_info);
        // Get the image dimensions
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $xmpWidth = $xmpData->AppliedToDimensions->W;
        $xmpHeight = $xmpData->AppliedToDimensions->H;

        $red = imagecolorallocate($image, 255, 0, 0); // Red for rectangles
        $white = imagecolorallocate($image, 255, 255, 255); // White for text background

        foreach ($xmpData->RegionList as $region) {
            $area = $region->Area;
            // Convert normalized values to pixel dimensions
            $centerX = $area->X * $imageWidth;
            $centerY = $area->Y* $imageHeight;
            $width =$area->W * $imageWidth;
            $height = $area->H * $imageHeight;
            // Calculate top-left and bottom-right corners
            $x1 = (int)($centerX - $width / 2);
            $y1 = (int)($centerY - $height / 2);
            $x2 = (int)($centerX + $width / 2);
            $y2 = (int)($centerY + $height / 2);

            // Draw 3 rectangles to simulate a 3px thick border
            for ($i = 0; $i < 10; $i++) {
                imagerectangle($image, $x1 - $i, $y1 - $i, $x2 + $i, $y2 + $i, $white);
            }
            // Add the name of the person (if available)
            if (isset($region->Name)) {
                $font = 5; // Built-in font size
                $textWidth = imagefontwidth($font) * strlen($region->Name);
                $textHeight = imagefontheight($font);
                $textX = (int)max(0, $x1 + ($width - $textWidth) / 2);
                $textY = (int)max(0, $y1 - $textHeight - 4); // 4px padding
                imagestring($image, $font, $textX, $textY, $region->Name, $white);
            }
        }

        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);

    }
}
