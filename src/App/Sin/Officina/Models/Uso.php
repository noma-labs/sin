<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $ofus_nome
 * @property string $ofus_abbr
 */
final class Uso extends Model
{
    protected $table = 'usi';

    protected $connection = 'db_officina';

    protected $primaryKey = 'ofus_iden';

    public function prenotazioniUso()
    {
        return $this->hasMany(Prenotazioni::class, 'uso_id', 'ofus_iden');
    }

    protected static function boot(): void
    {
        parent::boot();

        self::addGlobalScope('ordinamento', function (Builder $builder): void {
            $builder->orderBy('ordinamento');
        });
    }
}
