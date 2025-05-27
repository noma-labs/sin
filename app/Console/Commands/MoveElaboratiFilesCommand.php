<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Scuola\Models\Elaborato;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class MoveElaboratiFilesCommand extends Command
{
    protected $signature = 'elaborati:move-files';

    protected $description = 'Move elaborati files from nested folders to flat structure and update DB paths';

    public function handle(): void
    {
        $disk = Storage::disk('media_originals');
        $years = $disk->directories('elaborati');

        foreach ($years as $yearDir) {
            $nestedDirs = $disk->directories($yearDir);

            foreach ($nestedDirs as $nestedDir) {
                $files = $disk->files($nestedDir);

                foreach ($files as $filePath) {
                    $fileName = basename((string) $filePath);
                    $newPath = $yearDir.'/'.$fileName;

                    // Move the file if it doesn't already exist at the destination
                    if (! $disk->exists($newPath)) {
                        $disk->move($filePath, $newPath);
                        $this->info("Moved: $filePath -> $newPath");
                    } else {
                        $this->warn("Skipped (already exists): $newPath");
                    }

                    // Update DB if needed
                    $updated = Elaborato::query()->where('file_path', $filePath)
                        ->orWhere('file_path', 'like', "%$filePath")
                        ->update(['file_path' => $newPath]);

                    if ($updated) {
                        $this->info("Updated DB for: $newPath");
                    }
                }

                // Optionally, remove the now-empty nested directory
                if (empty($disk->files($nestedDir))) {
                    $disk->deleteDirectory($nestedDir);
                    $this->info("Deleted empty directory: $nestedDir");
                }
            }
        }

        $this->info('All files moved and DB updated.');

        return;
    }
}
