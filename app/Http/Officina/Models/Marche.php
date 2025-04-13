<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nome
 */
final class Marche extends Model
{
    protected $connection = 'db_officina';

    protected $table = 'marca';

    protected $primaryKey = 'id';

    public function modelli(): HasMany
    {
        return $this->hasMany(Modelli::class, 'marca_id');
    }
}
