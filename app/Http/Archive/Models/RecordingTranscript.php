<?php

declare(strict_types=1);

namespace App\Archive\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $code
 * @property int|null $recording_id
 * @property string|null $heading
 * @property string $file_path
 * @property string|null $content
 */
final class RecordingTranscript extends Model
{
    public $incrementing = false;

    protected $connection = 'archivio_nomadelfia';

    protected $table = 'recording_transcripts';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    protected $guarded = [];

    public function recording(): BelongsTo
    {
        return $this->belongsTo(Recording::class, 'recording_id');
    }

    protected function casts(): array
    {
        return ['recorded_date' => 'date'];
    }
}
