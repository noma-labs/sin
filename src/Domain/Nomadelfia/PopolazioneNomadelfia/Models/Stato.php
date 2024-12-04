<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Nomadelfia\Exceptions\StatoDoesNotExists;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $stato
 * @property string $nome
 */
final class Stato extends Model
{
    public $guarded = ['id'];

    public $timestamps = false;

    public static $mapNamesToDB = [
        'sacerdote' => 'SAC',
        'celibe' => 'CEL',
        'nubile' => 'NUB',
        'mammavocazione' => 'MAV',
        'sposato' => 'SPO',
    ];

    protected $connection = 'db_nomadelfia';

    protected $table = 'stati';

    protected $primaryKey = 'id';

    public static function find(string $name): self
    {
        $stato = self::where('stato', $name)->first();
        if (! $stato) {
            throw StatoDoesNotExists::create($name);
        }

        return $stato;
    }

    /**
     * Ritorna lo stato dal suo nome
     *
     * @author: Davide Neri
     */
    public static function perNome($nome)
    {

        return self::where('stato', self::$mapNamesToDB[$nome])->first();
    }

    public function persone()
    {
        return $this->belongsToMany(Persona::class, 'persone_stati', 'stato_id', 'persona_id')
            ->withPivot('stato')
            ->orderby('nominativo');
    }

    public function personeAttuale()
    {
        return $this->persone()->where('persone_stati.stato', '1');
    }

    public function scopeAttivo($query)
    {
        return $query->where('persone_stati.stato', 1);
    }

    public function isCelibe(): bool
    {
        return $this->stato === $this->mapNamesToDB['celibe'];
    }

    public function isNubile(): bool
    {
        return $this->stato === $this->mapNamesToDB['nubile'];
    }
}
