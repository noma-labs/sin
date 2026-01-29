<?php

declare(strict_types=1);

namespace App\Photo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents entries of the legacy DBF-mapped table holding stripe metadata.
 *
 * @property int $id
 * @property string|null $fingerprint
 * @property string $source
 * @property string $datnum
 * @property string $anum
 * @property string $cddvd
 * @property string $hdint
 * @property string $hdext
 * @property string $sc
 * @property string $fi
 * @property string $tp
 * @property int $nfo
 * @property string|null $data
 * @property string $localita
 * @property string $argomento
 * @property string $descrizione
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Photo> $photos
 */
final class DbfAll extends Model
{
    public $timestamps = false;

    protected $connection = 'db_foto';

    protected $table = 'dbf_all';

    protected $primaryKey = 'id';

    protected $guarded = [];

    /**
     * @return HasMany<Photo, covariant $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'dbf_id', 'id');
    }
}
