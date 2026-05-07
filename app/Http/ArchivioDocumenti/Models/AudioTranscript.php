<?php

declare(strict_types=1);

namespace App\ArchivioDocumenti\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $recorded_at
 * @property string|null $content
 * @property string $file_path
 */
final class AudioTranscript extends Model
{
    protected $connection = 'archivio_documenti';

    protected $table = 'audio_transcripts';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['recorded_at' => 'date'];
    }
}
