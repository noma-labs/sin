<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Photo\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use RuntimeException;

final class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        // Create a small fake image on disk and use its metadata
        $width = 800;
        $height = 600;

        // Read photos root from env; default to storage testing path
        $photosRootRaw = env('PHOTOS_PATH', storage_path('app/testing/photos'));
        $photosRoot = mb_rtrim(mb_trim((string) $photosRootRaw), DIRECTORY_SEPARATOR);
        if (! is_dir($photosRoot) && ! @mkdir($photosRoot, 0777, true)) {
            // Fallback if directory creation fails (permissions, etc.)
            $photosRoot = mb_rtrim(storage_path('app/testing/photos'), DIRECTORY_SEPARATOR);
            if (! is_dir($photosRoot)) {
                @mkdir($photosRoot, 0777, true);
            }
        }
        // Build a roll-style id like 00000-0 for file name
        $rollNumber = $this->faker->numberBetween(0, 10);
        $shotNumber = $this->faker->numberBetween(0, 9);
        $rollNumberPadded = str_pad((string) $rollNumber, 5, '0', STR_PAD_LEFT);
        $rollId = $rollNumberPadded.'-'.$shotNumber;
        $fileName = $rollId.'.jpg';
        $absolutePath = $photosRoot.DIRECTORY_SEPARATOR.$fileName;
        // Ensure parent directory exists (in case of nested paths in the future)
        $absoluteDir = dirname($absolutePath);
        if (! is_dir($absoluteDir)) {
            @mkdir($absoluteDir, 0777, true);
        }
        // Create placeholder image and get metadata (embed roll id for visibility)
        $meta = $this->createPlaceholderImage($absolutePath, $width, $height, 'ROLL '.$rollId);
        $mime = $meta['mime'];
        $width = $meta['width'];
        $height = $meta['height'];

        $size = is_file($absolutePath) ? filesize($absolutePath) : $this->faker->numberBetween(200, 4000);
        // Ensure SHA uniqueness across factory instances to avoid DB unique constraint collisions
        $sha = $this->faker->unique()->sha1();

        // Compute relative path (after PHOTOS_PATH) and directory without root or filename
        $relativePath = mb_ltrim(str_replace($photosRoot, '', $absolutePath), DIRECTORY_SEPARATOR);
        $relativeDir = dirname($relativePath);
        if ($relativeDir === '.' || $relativeDir === DIRECTORY_SEPARATOR) {
            $relativeDir = '';
        }

        return [
            'sha' => $sha,
            'source_file' => $relativePath,
            'subjects' => collect($this->faker->words(5))->join(', '),
            'file_size' => $size,
            'file_name' => basename($relativePath),
            'mime_type' => $mime,
            'image_height' => $height,
            'image_width' => $width,
            'directory' => $relativeDir,
            'taken_at' => Carbon::now(),
        ];
    }

    public function inFolder(string $name)
    {
        return $this->state(function (array $attributes) use ($name) {
            // build a folder title like: 2022-05-23 XXXMQ Argomento foto
            return [
                'directory' => $name,
            ];
        });
    }

    public function takenAt(Carbon $date)
    {
        return $this->state(function (array $attributes) use ($date) {
            return [
                'taken_at' => $date->toDateString(),
            ];
        });
    }

    /**
     * Create a simple placeholder image on disk and return its metadata.
     *
     * @return array{mime:string,width:int,height:int}
     */
    private function createPlaceholderImage(string $absolutePath, int $width, int $height, string $label, int $quality = 80): array
    {
        if (! function_exists('imagecreatetruecolor') || ! function_exists('imagejpeg')) {
            throw new RuntimeException('GD extension not available: functions imagecreatetruecolor and imagejpeg are required to generate test images. Please enable/install ext-gd.');
        }

        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            throw new RuntimeException('Failed to create image resource via GD.');
        }

        $bg = imagecolorallocate($image, 240, 240, 240);
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        if (function_exists('imagestring')) {
            $textColor = imagecolorallocate($image, 80, 80, 80);
            imagestring($image, 5, 10, 10, $label, $textColor);
        }

        $written = imagejpeg($image, $absolutePath, $quality);
        imagedestroy($image);

        if ($written !== true || ! is_file($absolutePath)) {
            throw new RuntimeException('Failed to write image to path: '.$absolutePath);
        }

        return [
            'mime' => 'image/jpeg',
            'width' => $width,
            'height' => $height,
        ];
    }
}
