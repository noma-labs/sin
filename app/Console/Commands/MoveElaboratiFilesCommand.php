<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\Models\Elaborato;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class MoveElaboratiFilesCommand extends Command
{
    protected $signature = 'elaborati:move';

    protected $description = 'Move elaborati files to storage/app/original/elaborati and rename them.';

    public function handle()
    {
        $elaborati = Elaborato::all();

        foreach ($elaborati as $elaborato) {
            $as = AnnoScolastico::fromString($elaborato->anno_scolastico);
            // $this->moveFilePath($elaborato, $as);
            $this->moveCoverImagePath($elaborato, $as);
        }

        $this->info('File moving process completed.');
    }

    private function moveFilePath(Elaborato $elaborato, AnnoScolastico $as): void
    {
        $filePath = $elaborato->file_path;
        if ($filePath === null) {
            return;
        }

        if (Storage::disk('scuola')->exists($filePath)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'pdf';
            $destinationPath = "elaborati/{$as->endYear}_{$elaborato->collocazione}.{$extension}";

            $contents = Storage::disk('scuola')->get($filePath);

            // Write file to media_originals disk under elaborati/
            Storage::disk('media_originals')->put($destinationPath, $contents);

            // Delete original file from scuola disk
            Storage::disk('scuola')->delete($filePath);

            // Update DB with new file path
            $elaborato->file_path = $destinationPath;
            $elaborato->save();

            $this->info("Moved: {$filePath} to media_originals/{$destinationPath} and updated DB.");

            return;
        }

    }

    private function moveCoverImagePath(Elaborato $elaborato, AnnoScolastico $as): void
    {
        $coverPath = $elaborato->cover_image_path;
        if ($coverPath === null) {
            return;
        }
        if (Storage::disk('public')->exists($coverPath)) {
            $coverExtension = pathinfo($coverPath, PATHINFO_EXTENSION) ?: 'png';

            $coverDestinationPath = "elaborati/{$as->endYear}_{$elaborato->collocazione}.{$coverExtension}";

            $coverContents = Storage::disk('public')->get($coverPath);
            Storage::disk('media_previews')->put($coverDestinationPath, $coverContents);
            Storage::disk('public')->delete($coverPath);

            $elaborato->cover_image_path = $coverDestinationPath;
            $elaborato->save();

            $this->info("Moved cover image: {$coverPath} to media_previews/{$coverDestinationPath} and updated DB.");
        }
    }
}
