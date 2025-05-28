<?php

declare(strict_types=1);

namespace App\Agraria\Models;

use Database\Factories\ManutenzioneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $data
 * @property int $ore
 * @property float $spesa
 * @property string $persona
 * @property string|null $lavori_extra
 * @property int $mezzo_agricolo
 */
final class Manutenzione extends Model
{
    /** @use HasFactory<ManutenzioneFactory> */
    use HasFactory;

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

    protected static function booted(): void
    {
        // if a new maintenance is created for a vehicle and the hour inserted are greater than the total hours of the vehicle:
        //    1) update the total hours of the vehicle
        //    2) insert a new record in the historical table
        self::created(function (Manutenzione $manutenzione): void {
            $mezzo = MezzoAgricolo::find($manutenzione->mezzo_agricolo);
            if ($manutenzione->ore > $mezzo->tot_ore) {
                $so = new StoricoOre;
                $so->data = now()->toDateString();
                $so->ore = $manutenzione->ore - $mezzo->tot_ore;
                $so->mezzo_agricolo = $mezzo->id;
                $so->save();

                $mezzo->tot_ore = $manutenzione->ore;
                $mezzo->save();
            }
        });
    }

    protected static function newFactory(): ManutenzioneFactory
    {
        return ManutenzioneFactory::new();
    }
}
