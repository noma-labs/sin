<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\Persona\Models;

use Illuminate\Database\Eloquent\Model;

final class NominativoStorico extends Model
{
    protected $connection = 'db_nomadelfia';

    protected $table = 'nominativi_storici';

    protected $primaryKey = 'persona_id';

    protected $guarded = [];
}
