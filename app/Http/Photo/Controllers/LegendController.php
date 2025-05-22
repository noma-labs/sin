<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class LegendController
{
    public function index(): StreamedResponse
    {
        $photos = Photo::query()
            ->where('favorite', 1)
            ->orderBy('taken_at')
            ->get();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'marginTop' => 400,    // 0.4 cm
            'marginBottom' => 400,
            'marginLeft' => 400,
            'marginRight' => 400,
        ]);

        foreach ($photos as $photo) {
            // Museum-style: Date (bold), Location (italic), Description (normal)
            $date = '';
            if ($photo->taken_at) {
                $date = $photo->taken_at->format('m-d') === '01-01'
                    ? $photo->taken_at->format('Y')
                    : $photo->taken_at->format('d/m/Y');
            }

            $location = $photo->location ?: '?';
            $description = $photo->description ?: '';

            $section->addText("$date", ['bold' => true]);
            $section->addText("$location", ['italic' => true]);
            $section->addText("$description");
            $section->addTextBreak(1);
        }

        $fileName = 'photo_'.now()->format('Ymd_His').'.docx';

        return response()->streamDownload(function () use ($phpWord) {
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }
}
