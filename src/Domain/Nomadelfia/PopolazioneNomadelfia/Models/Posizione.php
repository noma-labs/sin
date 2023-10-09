<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Nomadelfia\Exceptions\PosizioneDoesNotExists;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $abbreviato
 */
class Posizione extends Model
{
    protected $connection = 'db_nomadelfia';

    protected $table = 'posizioni';

    protected $primaryKey = 'id';

    protected static $mapNamesToDB = [
        'effettivo' => 'EFFE',
        'postulante' => 'POST',
        'ospite' => 'OSPP',
        'figlio' => 'FIGL',
        'uscito' => 'DADE',
    ];

    /**
     * Ordina (di default) le posizioni secondo la colonna ordinamento
     *
     * @author: Davide Neri
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ordinamento', function (Builder $builder) {
            $builder->orderBy('ordinamento');
        });
    }

    public function persone()
    {
        return $this->belongsToMany(Persona::class, 'persone_posizioni', 'posizione_id', 'persona_id')
            ->withPivot('stato')
            ->orderby('nominativo');
    }

    public function personeAttuale()
    {
        return $this->persone()->where('persone_posizioni.stato', '1');
    }

    /**
     * Find a Posizione by its name
     *
     * @param string $name abbreviato
     * @return  Posizione;
     *
     * @throws \App\Nomadelfia\Exceptions\PosizioneDoesNotExists
     */
    public static function find(string $name): Posizione
    {
        $posizione = Posizione::where('abbreviato', $name)->first();
        if (! $posizione) {
            throw PosizioneDoesNotExists::named($name);
        }

        return $posizione;
    }

    /**
     * Ritorna la posizione dal nome
     *
     * @author: Davide Neri
     */
    public static function perNome($nome)
    {
        return static::where('abbreviato', self::$mapNamesToDB[$nome])->first();
    }

    public function isPostulante()
    {
        return $this->abbreviato == self::$mapNamesToDB['postulante'];
    }

    public function isEffettivo()
    {
        return $this->abbreviato == self::$mapNamesToDB['effettivo'];
    }
}
