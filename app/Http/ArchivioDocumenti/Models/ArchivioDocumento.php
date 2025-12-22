<?php

declare(strict_types=1);

namespace App\ArchivioDocumenti\Models;

use App\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $foglio
 * @property string $titolo
 */
final class ArchivioDocumento extends Model
{
    use SortableTrait;

    public $timestamps = false;

    protected $connection = 'archivio_documenti';

    protected $table = 'libri';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected function scopeTobePrinted($query)
    {
        return $query->where('stato', 1)->orderBy('foglio');
    }
}
