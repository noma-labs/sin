<?php

declare(strict_types=1);

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class MezzoAgricolo extends Model
{
    public $timestamps = false;

    protected $connection = 'db_agraria';

    protected $table = 'mezzo_agricolo';

    public function gommeAnt()
    {
        return $this->hasOne(Gomma::class, 'id', 'gomme_ant');
    }

    public function gommePos()
    {
        return $this->hasOne(Gomma::class, 'id', 'gomme_post');
    }

    public function scadenzaManutenzioni()
    {
        $prog = ManutenzioneProgrammata::where('ore', '>', 10)->get();
        $scadenze = [];
        foreach ($prog as $p) {
            $man = Manutenzione::whereHas('programmate', function (Builder $q) use ($p): void {
                $q->where('tipo', $p->id);
            })
                ->where('mezzo_agricolo', $this->id)
                ->orderBy('data', 'desc')
                ->first();
            if ($man === null) {
                $scadenze[$p->nome] = $p->ore - $this->tot_ore;
            } else {
                $scadenze[$p->nome] = $p->ore - ($this->tot_ore - $man->ore);
            }
        }

        return collect($scadenze);
    }
}
