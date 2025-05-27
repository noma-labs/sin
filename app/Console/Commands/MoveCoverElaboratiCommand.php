<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Scuola\Models\Elaborato;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class MoveCoverElaboratiCommand extends Command
{
    protected $signature = 'elaborati:move';

    protected $description = 'Move elaborati files to storage/app/original/elaborati and rename them.';

    public function handle(): void
    {

        $movedCount = $this->moveCoverImagePath();
        $this->info("File moving process completed. Moved: {$movedCount} images.");
    }

    private function moveCoverImagePath(): int
    {
        $elaborati = Elaborato::query()->whereNotNull('cover_image_path')->get();

        $count = 0;
        foreach ($elaborati as $elaborato) {
            /** @var Elaborato $elaborato */
            $coverPath = $elaborato->cover_image_path;

            if (empty($elaborato->collocazione)) {
                throw new RuntimeException("Elaborato ID {$elaborato->id} has null collocazione. Aborting.");
            }

            if (Storage::disk('public')->exists($coverPath)) {
                $coverContents = Storage::disk('public')->get($coverPath);
                if ($coverContents === null) {
                    $this->error("File {$coverPath} non trovato per {$elaborato->collocazione}");
                }

                $coverExtension = pathinfo($coverPath, PATHINFO_EXTENSION) ?: 'png';

                $coverDestinationPath = "elaborati/{$elaborato->collocazione}.{$coverExtension}";
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
