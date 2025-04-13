<?php

declare(strict_types=1);

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Model;

final class StoricoOre extends Model
{
    public $timestamps = false;

    protected $connection = 'db_agraria';

    protected $table = 'storico_ore';

    protected $guarded = [];
}
