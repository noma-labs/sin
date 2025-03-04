<?php

declare(strict_types=1);

namespace App\Biblioteca\Models;

use App\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Model;

final class Video extends Model
{
    use SortableTrait;

    protected $connection = 'db_biblioteca';

    protected $table = 'video';

    protected $primaryKey = 'id';

    protected function casts(): array
    {
        return ['data_registrazione' => 'datetime'];
    }
}
