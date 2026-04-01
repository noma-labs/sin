<?php

declare(strict_types=1);

namespace App\Photo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $photo_id
 * @property int|null $persona_id
 * @property string $issue_type
 * @property string|null $photo_persona_name
 * @property Carbon|null $resolved_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class PhotoIssue extends Model
{
    protected $connection = 'db_foto';

    protected $table = 'photos_issues';

    protected $fillable = [
        'photo_id',
        'persona_id',
        'issue_type',
        'photo_persona_name',
    ];

    /** @return BelongsTo<Photo, $this> */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }
}
