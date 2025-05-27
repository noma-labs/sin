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
        $elaborati = Elaborato::whereNotNull('cover_image_path')->get();

        foreach ($elaborati as $elaborato) {
            $as = AnnoScolastico::fromString($elaborato->anno_scolastico);
            $this->moveCoverImagePath($elaborato, $as);
        }

        $this->info('File moving process completed.');
    }

    private function moveCoverImagePath(Elaborato $elaborato, AnnoScolastico $as): void
    {
        $coverPath = $elaborato->cover_image_path;
        if ($coverPath === null) {
            return;
        }
        if (Storage::disk('public')->exists($coverPath)) {
            $coverExtension = pathinfo($coverPath, PATHINFO_EXTENSION) ?: 'png';

            $coverDestinationPath = "elaborati/{$elaborato->collocazione}.{$coverExtension}";

            $coverContents = Storage::disk('public')->get($coverPath);
            Storage::disk('media_previews')->put($coverDestinationPath, $coverContents);
            Storage::disk('public')->delete($coverPath);

            $elaborato->cover_image_path = $coverDestinationPath;
            $elaborato->save();

            $this->info("Moved cover image: {$coverPath} to media_previews/{$coverDestinationPath} and updated DB.");
        }
    }
}
