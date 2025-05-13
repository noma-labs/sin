<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use App\Photo\Models\PhotoEnrico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class PhotoController
{
    public function index(Request $request)
    {
        $filterYear = $request->input('year');

        $enrico = PhotoEnrico::orderBy('data');
        if ($filterYear !== null) {
            $enrico = $enrico->orWhere('descrizione', 'like', '%'.$filterYear."%");
            $enrico = $enrico->orwhereRaw('YEAR(data)= ?', [$filterYear]);
        }
        $enrico = $enrico->get();

        $q = Photo::orderBy('taken_at')
            ->where('favorite', 1)
            ->orderBy('taken_at');
        if ($filterYear !== null) {
            $q->whereRaw('YEAR(taken_at)= ?', [$filterYear]);
        }

        $photos = $q->get();
        $photos_count = $q->count();

        $years = Photo::selectRaw('YEAR(taken_at) as year, count(*) as `count` ')
            ->groupByRaw('YEAR(taken_at)')
            ->where('favorite', 1)
            ->orderByRaw('YEAR(taken_at)')
            ->get();

        return view('photo.index', compact('photos', 'photos_count', 'years', 'enrico'));
    }

    public function update(Request $request, string $sha)
    {
        $request->validate([
            'taken_at' => 'nullable|date',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
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

        return redirect()->back()->with('success', 'Foto aggiornata correttamente');
    }

    public function show(Request $request, string $sha)
    {
        $photo = Photo::where('sha', $sha)->firstOrFail();

        if (!Storage::disk('photos')->exists($photo->source_file)) {
            abort(404, 'File not found.');
        }

        $people = DB::connection('db_foto')
            ->table('foto_persone')
            ->select('p.id', 'foto_persone.persona_nome', 'e.FOTO', 'e.NOME', 'e.COGNOME', 'e.ALIAS', 'e.NASCITA')
            ->leftJoin('db_nomadelfia.alfa_enrico_15_feb_23 as e', 'e.FOTO', '=', 'foto_persone.persona_nome')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id_alfa_enrico', '=', 'e.id')
            ->where('foto_persone.photo_id', '=', $photo->uid)
            ->orderby('foto_persone.persona_nome')
            ->get();

        return view('photo.show', [
            'photo' => $photo,
            'people' => $people,
        ]);
    }

    public function preview(Request $request, string $sha)
    {
        $photo = Photo::where('sha', $sha)->firstOrFail();

        $filePath = $photo->source_file;

        if (! Storage::disk('photos')->exists($filePath)) {
            abort(404);
        }

        $fileContent = Storage::disk('photos')->get($filePath);
        $filePath= Storage::disk('photos')->path($filePath);
        $mimeType = Storage::disk('photos')->mimeType($filePath);

        $drawFaces = $request->query('draw_faces', false);

        if (!$drawFaces){
            return response($fileContent, 200)->header('Content-Type', $mimeType);
        }

        if ($photo->region_info){
            $image = imagecreatefromjpeg($filePath);
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

                if (isset($region->Name)) {
                    $name = $region->Name;
                    $font = 5; // Built-in font size
                    $textWidth = imagefontwidth($font) * mb_strlen($name);
                    $textHeight = imagefontheight($font);
                    $textX = (int) max(0, $x1 + ($width - $textWidth) / 2);
                    $textY = (int) max(0, $y1 - $textHeight - 4); // 4px padding
                    imagestring($image, $font, $textX, $textY, $name, $white);
                }
            }

            ob_start();
            imagejpeg($image);
            $imageData = ob_get_clean();

            imagedestroy($image);

            return response($imageData, 200)->header('Content-Type', $mimeType);
        }


        return response($fileContent, 200)->header('Content-Type', $mimeType);
    }


    public function download(string $sha)
    {
        $photo = Photo::where('sha', $sha)->firstOrFail();

        if (!Storage::disk('photos')->exists($photo->source_file)) {
            abort(404, 'File not found.');
        }

        return  Storage::disk('photos')->download($photo->source_file);
    }
}
