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

        $years = Photo::selectRaw("YEAR(taken_at) as year, count(*) as `count` ")
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

        // Load the image
        $image = imagecreatefromjpeg($filepath);
        if (!$image) {
            die("Failed to load image.");
        }

    // Example XMP metadata (replace this with actual data)
    $xmpData = [
        "AppliedToDimensions" => [
            "H" => 1293,
            "Unit" => "pixel",
            "W" => 1973
        ],
        "RegionList" => [
            [
                "Area" => [
                    "H" => 0.07069,
                    "W" => 0.04632,
                    "X" => 0.53135,
                    "Y" => 0.58358
                ],
                "Name" => "ENRICO BE",
                "Rotation" => 0.06687,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.06623,
                    "W" => 0.04340,
                    "X" => 0.54327,
                    "Y" => 0.35990
                ],
                "Name" => "BONI EL",
                "Rotation" => -0.02023,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.07215,
                    "W" => 0.04728,
                    "X" => 0.37834,
                    "Y" => 0.37718
                ],
                "Name" => "MIRCO BO",
                "Rotation" => 0.18237,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.07019,
                    "W" => 0.04600,
                    "X" => 0.74132,
                    "Y" => 0.37392
                ],
                "Name" => "FAUSTO PE",
                "Rotation" => 0.02025,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.06845,
                    "W" => 0.04485,
                    "X" => 0.30897,
                    "Y" => 0.58739
                ],
                "Name" => "AZIO RO",
                "Rotation" => 0.02100,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.06684,
                    "W" => 0.04380,
                    "X" => 0.12674,
                    "Y" => 0.35969
                ],
                "Name" => "VANDO RO",
                "Rotation" => 0.09875,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.06767,
                    "W" => 0.04434,
                    "X" => 0.66531,
                    "Y" => 0.38419
                ],
                "Name" => "MASELLI CARLO",
                "Rotation" => 0.15826,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.06623,
                    "W" => 0.04340,
                    "X" => 0.46951,
                    "Y" => 0.37042
                ],
                "Name" => "MIRCO BO",
                "Rotation" => 0.05550,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.07187,
                    "W" => 0.04709,
                    "X" => 0.24921,
                    "Y" => 0.33325
                ],
                "Name" => "PIETRO VA",
                "Rotation" => 0.09630,
                "Type" => "Face"
            ],
            [
                "Area" => [
                    "H" => 0.08355,
                    "W" => 0.05475,
                    "X" => 0.86553,
                    "Y" => 0.34554
                ],
                "Name" => "GIORGIO PED",
                "Rotation" => -0.05186,
                "Type" => "Face"
            ]
        ]
    ];
    // Get the image dimensions
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);

    // dd($imageWidth)
    // Get the dimensions from the XMP metadata
    $xmpWidth = $xmpData['AppliedToDimensions']['W'];
    $xmpHeight = $xmpData['AppliedToDimensions']['H'];

    // Allocate colors
    $red = imagecolorallocate($image, 255, 0, 0); // Red for rectangles
    $white = imagecolorallocate($image, 255, 255, 255); // White for text background

    // Draw rectangles for each region
    foreach ($xmpData['RegionList'] as $region) {
        $area = $region['Area'];

        // Convert normalized coordinates to absolute pixel values
        $x = $area['X'] * $xmpWidth * ($imageWidth / $xmpWidth);
        $y = $area['Y'] * $xmpHeight * ($imageHeight / $xmpHeight);
        $width = $area['W'] * $xmpWidth * ($imageWidth / $xmpWidth);
        $height = $area['H'] * $xmpHeight * ($imageHeight / $xmpHeight);

        // Draw the rectangle
        imagerectangle($image, (int)$x, (int)$y, (int)($x + $width), (int)($y + $height), $red);

        // Add the name of the person (if available)
        if (isset($region['Name'])) {
            $fontSize = 3; // Font size for text
            $textX = $x;
            $textY = $y - 12; // Position the text above the rectangle
            $text = $region['Name'];

            // Draw a white background for the text
            imagefilledrectangle($image, (int)$textX, (int) $textY, (int)$textX + strlen($text) * 7, (int) $textY + 12, $white);

            // Draw the text
            imagestring($image, $fontSize, (int)$textX, (int) $textY, $text, $red);
        }
    }

    header('Content-Type: image/jpeg');
    imagejpeg($image);
    imagedestroy($image);

    return ;

}

}
