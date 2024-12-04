<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Nomadelfia\Exceptions\PosizioneDoesNotExists;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $abbreviato
 * @property string $nome
 */
final class Posizione extends Model
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
     * Find a Posizione by its name
     *
     * @param  string  $name  abbreviato
     * @return \Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
     *
     * @throws PosizioneDoesNotExists
     */
    public static function find(string $name): self
    {
        $posizione = self::where('abbreviato', $name)->first();
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
        return self::where('abbreviato', self::$mapNamesToDB[$nome])->first();
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

    public function isPostulante(): bool
    {
        return $this->abbreviato === self::$mapNamesToDB['postulante'];
    }

    public function isEffettivo(): bool
    {
        return $this->abbreviato === self::$mapNamesToDB['effettivo'];
    }

    /**
     * Ordina (di default) le posizioni secondo la colonna ordinamento
     *
     * @author: Davide Neri
     */
    protected static function boot(): void
    {
        parent::boot();

        self::addGlobalScope('ordinamento', function (Builder $builder): void {
            $builder->orderBy('ordinamento');
        });
    }
}
