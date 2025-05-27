<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\Models\Elaborato;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class MoveCoverElaboratiCommand extends Command
{
    protected $signature = 'elaborati:move';

    protected $description = 'Move elaborati files to storage/app/original/elaborati and rename them.';

    public function handle()
    {

        $movedCount = $this->moveCoverImagePath();
        $this->info("File moving process completed. Moved: {$movedCount} images.");
    }

    private function moveCoverImagePath(): int
    {
        $elaborati = Elaborato::whereNotNull('cover_image_path')->get();

        $count = 0;
        foreach ($elaborati as $elaborato) {
            $coverPath = $elaborato->cover_image_path;

            if (Storage::disk('public')->exists($coverPath)) {
                $coverExtension = pathinfo($coverPath, PATHINFO_EXTENSION) ?: 'png';

                $coverDestinationPath = "elaborati/{$elaborato->collocazione}.{$coverExtension}";

                $coverContents = Storage::disk('public')->get($coverPath);
                Storage::disk('media_previews')->put($coverDestinationPath, $coverContents);
                Storage::disk('public')->delete($coverPath);

                $elaborato->cover_image_path = $coverDestinationPath;
                $elaborato->save();

                $this->info("Moved cover image: {$coverPath} to media_previews/{$coverDestinationPath} and updated DB.");
                $count++;
            }
        }
        return $count;
    }
}
