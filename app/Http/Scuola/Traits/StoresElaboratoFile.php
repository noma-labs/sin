<?php

declare(strict_types=1);

namespace App\Scuola\Traits;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait StoresElaboratoFile
{
    /**
     * Store the elaborato file and return its storage path.
     */
    protected function storeElaboratoFile(
        UploadedFile $file,
        AnnoScolastico $as,
        string $collocazione,
        string $titolo
    ): string {
        $titleSlug = Str::slug($titolo);

        $destinationPath = "elaborati/{$as->endYear}_{$collocazione}_{$titleSlug}.{$file->getClientOriginalExtension()}";
        if (Storage::disk('media_originals')->put($destinationPath, $file)) {
            return $destinationPath;
        }
        throw new Exception('Error storing into disk');
    }
}
