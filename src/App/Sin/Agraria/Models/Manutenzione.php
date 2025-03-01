<?php

declare(strict_types=1);

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id.
 * @property string $lavori_extra
 */
final class Manutenzione extends Model
{
    public $timestamps = false;

    protected $connection = 'db_agraria';

    protected $table = 'manutenzione';

    protected $guarded = [];

    public function programmate()
    {
        return $this->belongsToMany(ManutenzioneProgrammata::class, 'manutenzione_tipo', 'manutenzione', 'tipo');
    }

    public function mezzo()
    {
        return $this->hasOne(MezzoAgricolo::class, 'id', 'mezzo_agricolo');
    }
}
