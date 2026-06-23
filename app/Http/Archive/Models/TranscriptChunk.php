<?php

declare(strict_types=1);

namespace App\Archive\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $recording_transcript_id
 * @property int $chunk_index
 * @property string $content
 * @property array|null $embedding
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class TranscriptChunk extends Model
{
    protected $connection = 'archivio_nomadelfia';

    protected $table = 'recording_transcript_chunks';

    public $incrementing = false;

    protected $primaryKey = null;

    protected $guarded = [];

    public function transcript(): BelongsTo
    {
        return $this->belongsTo(RecordingTranscript::class, 'recording_transcript_id');
    }

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
        ];
    }
}
