<?php

declare(strict_types=1);

namespace App\Photo\Models;

use App\Nomadelfia\Persona\Models\Persona;
use Database\Factories\PhotoFactory;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Photo
 *
 * Represents a photo entity with metadata extracted from the database schema.
 *
 * @property string $uid Unique identifier for the photo.
 * @property string $sha Unique SHA hash of the photo file.
 * @property string $source_file Path to the source file of the photo.
 * @property string|null $directory Directory where the photo is stored.
 * @property int|null $file_size Size of the photo file in bytes.
 * @property string|null $file_name Name of the photo file.
 * @property string|null $file_type MIME type of the photo file.
 * @property string|null $file_type_extension File extension of the photo.
 * @property int|null $image_height Height of the photo in pixels.
 * @property int|null $image_width Width of the photo in pixels.
 * @property string|null $keywords Keywords associated with the photo.
 * @property string|null $region_info JSON-encoded region information (e.g., face detection data).
 * @property string|null $subject Subject or theme of the photo.
 * @property bool|null $favorite Indicates if the photo is marked as a favorite.
 * @property string|null $description Description of the photo.
 * @property string|null $location Location where the photo was taken.
 * @property DateTime|string|null $taken_at Date and time when the photo was taken (set by the camera).
 * @property DateTime|string $created_at Timestamp when the photo record was created.
 * @property DateTime|string $updated_at Timestamp when the photo record was last updated.
 */
final class Photo extends Model
{
    /** @use HasFactory<PhotoFactory> */
    use HasFactory;

    public $timestamps = true;

    protected $connection = 'db_foto';

    protected $table = 'photos';

    protected $primaryKey = 'uid';

    protected $guarded = [];

    /**
     * The people associated with the photo.
     *
     * @return BelongsToMany<Persona, covariant $this>
     */
    public function persone(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'db_foto.foto_persone', 'photo_id', 'persona_id');
    }

    protected static function newFactory(): PhotoFactory
    {
        return PhotoFactory::new();
    }

    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
            'uid' => 'string',
        ];
    }
}
