<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use App\Photo\Models\PhotoEnrico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->where('favorite', 1)
            ->orderByRaw('YEAR(taken_at)')
            ->get();

        return view('photo.index', compact('photos', 'years', 'enrico'));
    }

    public function update(Request $request, string $sha)
    {
        $request->validate([
            'taken_at' => 'nullable|date', // Allow null for taken_at
            'description' => 'nullable|string', // Allow null for description
            'location' => 'nullable|string', // Allow null for description
        ]);

        $photo = Photo::where('sha', $sha)->firstOrFail();

        if ($request->filled('taken_at')) {
            $photo->taken_at = $request->input('taken_at');
        }

        if ($request->filled('description')) {
            $photo->description = $request->input('description');
        }

        if ($request->filled('location')) {
            $photo->location = $request->input('location');
        }

        $photo->save();

        return redirect()->route('photos.show', $sha)->with('success', 'Photo updated successfully.');
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
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $white = imagecolorallocate($image, 255, 255, 255);
        foreach ($xmpData->RegionList as $region) {
            $area = $region->Area;
            // Convert normalized values to pixel dimensions
            $centerX = $area->X * $imageWidth;
            $centerY = $area->Y * $imageHeight;
            $width = $area->W * $imageWidth;
            $height = $area->H * $imageHeight;
            // Calculate top-left and bottom-right corners
            $x1 = (int) ($centerX - $width / 2);
            $y1 = (int) ($centerY - $height / 2);
            $x2 = (int) ($centerX + $width / 2);
            $y2 = (int) ($centerY + $height / 2);

            // Draw 3 rectangles to simulate a 3px thick border
            for ($i = 0; $i < 5; $i++) {
                imagerectangle($image, $x1 - $i, $y1 - $i, $x2 + $i, $y2 + $i, $white);
            }
            $name = $region->Name;
            // Add the name of the person (if available)
            if (isset($name)) {
                $font = 5; // Built-in font size
                $textWidth = imagefontwidth($font) * mb_strlen($name);
                $textHeight = imagefontheight($font);
                $textX = (int) max(0, $x1 + ($width - $textWidth) / 2);
                $textY = (int) max(0, $y1 - $textHeight - 4); // 4px padding
                imagestring($image, $font, $textX, $textY, $name, $white);
            }
        }
        // Save the modified image to a publicly accessible directory
        $publicTempDir = storage_path('app/public/temp');
        if (! file_exists($publicTempDir)) {
            mkdir($publicTempDir, 0755, true); // Create the directory if it doesn't exist
        }
        $tempFileName = uniqid('photo_', true).'.jpg';
        $tempFilePath = $publicTempDir.DIRECTORY_SEPARATOR.$tempFileName;
        imagejpeg($image, $tempFilePath);
        imagedestroy($image);

        // Generate a URL for the temporary file
        $tempFileUrl = asset('storage/temp/'.$tempFileName);

        $people = DB::connection('db_foto')
            ->table('foto_persone')
            ->select('p.id', 'foto_persone.persona_nome', 'e.FOTO', 'e.NOME', 'e.COGNOME', 'e.ALIAS', 'e.NASCITA')
            ->leftJoin('db_nomadelfia.alfa_enrico_15_feb_23 as e', 'e.FOTO', '=', 'foto_persone.persona_nome')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id_alfa_enrico', '=', 'e.id')
            ->where('foto_persone.photo_id', '=', $photo->uid)
            ->get();

        // Pass the photo and temporary file URL to the Blade view
        return view('photo.show', [
            'photo' => $photo,
            'tempFileUrl' => $tempFileUrl,
            'people' => $people,
        ]);
    }
}
