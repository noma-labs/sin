<?php

declare(strict_types=1);

namespace App\Photo\Models;

use App\Nomadelfia\Persona\Models\Persona;
use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PhotoEnrico extends Model
{

    protected $connection = 'db_foto';

    protected $table = 'foto_enrico';

}
