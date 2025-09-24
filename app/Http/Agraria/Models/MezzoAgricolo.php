<?php

declare(strict_types=1);

namespace App\Agraria\Models;

use Database\Factories\MezzoAgricoloFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int i$id.
 * @property string $nome
 * @property int $tot_ore.
 * @property string $numero_telaio
 * @property string $filtro_olio
 * @property string $filtro_gasolio
 * @property string $filtro_servizi
 * @property string $filtro_aria_int
 * @property int $gomme_ant
 * @property int $gomme_post
 */
final class MezzoAgricolo extends Model
{
    /** @use HasFactory<MezzoAgricoloFactory> */
    use HasFactory;

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
                // it happens when there is a mistake and there is a maintanence with hour that are greater than the total hours of the vehicle.
                if ($man->ore > $this->tot_ore) {
                    $scadenze[$p->nome] = $p->ore - $this->tot_ore;
                } else {
                    $scadenze[$p->nome] = $p->ore - ($this->tot_ore - $man->ore);
                }
            }

        }

        return collect($scadenze);
    }

    protected static function newFactory(): MezzoAgricoloFactory
    {
        return MezzoAgricoloFactory::new();
    }
}
