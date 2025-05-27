<?php

declare(strict_types=1);

namespace App\Scuola\Traits;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait StoresElaboratoFile
{
    protected function storeElaboratoFile(
        UploadedFile $file,
        AnnoScolastico $as,
        string $collocazione,
        string $titolo
    ): string {
        $titleSlug = Str::slug($titolo);

        $destinationPath = $file->storeAs("elaborati/{$as->endYear}", "{$collocazione}_{$titleSlug}.{$file->getClientOriginalExtension()}", 'media_originals');
        if (! $destinationPath) {
            throw new Exception('Error storing to disk Request', 1);
        }

        return $destinationPath;
    }
}
