<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Traits\SortableTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder sortable()
 *
 * @property int $id
 * @property string $data_nascita
 * @property string $numero_elenco
 * @property string $cognome
 * @property string $nome
 * @property string $nominativo
 * @property string $sesso
 * @property string $provincia_nascita
 * @property string $cf
 * @property string $data_entrata
 * @property string $posizione
 * @property string $stato
 * @property string $gruppo
 * @property string $famiglia
 * @property string $azienda
 */
final class PopolazioneAttuale extends Model
{
    use SortableTrait;

    public $timestamps = false;

    protected $connection = 'db_nomadelfia';

    protected $table = 'v_popolazione_attuale';

    protected $guarded = [];

    public function scopeMale(Builder $query): Builder
    {
        return $query->where('sesso', 'M');
    }

    public function scopeFemale(Builder $query): Builder
    {
        return $query->where('sesso', 'F');
    }

    public function scopeOverage(Builder $query): Builder
    {
        return $query->where('data_nascita', '<=', Carbon::now()->subYears(18));
    }

    public function scopeUnderage(Builder $query): Builder
    {
        return $query->where('data_nascita', '>', Carbon::now()->subYears(18));
    }

    public function scopeEffettivo(Builder $query): Builder
    {
        return $query->where('posizione', 'Effettivo');
    }

    public function scopePostulante(Builder $query): Builder
    {
        return $query->where('posizione', 'Postulante');
    }

    public function scopeOspite(Builder $query): Builder
    {
        return $query->where('posizione', 'Ospite');
    }

    public function scopeFiglio(Builder $query): Builder
    {
        return $query->where('posizione', 'Figlio');
    }
}
