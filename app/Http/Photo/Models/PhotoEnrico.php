<?php

declare(strict_types=1);

namespace App\Photo\Models;

use Illuminate\Database\Eloquent\Model;

final class PhotoEnrico extends Model
{
    protected $connection = 'db_foto';

    protected $table = 'dbf_foto_enrico';
}
