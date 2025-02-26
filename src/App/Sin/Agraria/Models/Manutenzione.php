<?php

declare(strict_types=1);

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function lavoriToString(): string
    {
        $res = [];
        if ($this->lavori_extra !== null && ! ctype_space($this->lavori_extra)) {
            $res[] = mb_strtolower($this->lavori_extra);
        }
        $prog = $this->programmate()->get();
        if ($prog->isNotEmpty()) {
            foreach ($prog as $p) {
                $res[] = mb_strtolower($p->nome);
            }
        }

        return implode(', ', $res);
    }
}
