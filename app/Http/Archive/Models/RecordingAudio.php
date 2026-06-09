<?php

declare(strict_types=1);

namespace App\Archive\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $recording_id
 * @property string|null $code
 * @property string $file_name
 * @property string $file_path
 * @property int $file_size_bytes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class RecordingAudio extends Model
{
    protected $connection = 'archivio_nomadelfia';

    protected $table = 'recording_audio';

    protected $guarded = [];

    /**
     * @return BelongsTo<Recording, self>
     */
    public function recording(): BelongsTo
    {
        return $this->belongsTo(Recording::class, 'recording_id');
    }

    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'int',
        ];
    }
}
