<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Exceptions\StatoDoesNotExists;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * A stato of a person that cannot be derived from the famiglia: sacerdote, separato,
 * vedovo for a person whose coniuge is not in the anagrafe (see PersonaInNomadelfia::statoCivile()).
 * The stato civile (celibe, nubile, sposato, vedovo) is otherwise derived by PersonaInNomadelfia::statoCivile(),
 * mamma di vocazione, nomadelfa mamma, mamma nubile and celibe di elezione by the tipo of the famiglia the person heads (TipoFamiglia).
 *
 * @property string $stato
 * @property string $nome
 */
final class Stato extends Model
{
    public $guarded = ['id'];

    public $timestamps = false;

    /**
     * @var array<string, string>
     */
    public static array $mapNamesToDB = [
        'sacerdote' => 'SAC',
        'separato' => 'SEP',
        'vedovo' => 'VED',
    ];

    protected $connection = 'db_nomadelfia';

    protected $table = 'stati';

    protected $primaryKey = 'id';

    /**
     * @return Collection<int, self>
     */
    public static function assegnabili(): Collection
    {
        return self::all()->sortBy('nome')->values();
    }

    public static function find(string $name): self
    {
        $stato = self::where('stato', $name)->first();
        if (! $stato) {
            throw StatoDoesNotExists::create($name);
        }

        return $stato;
    }

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

    /**
     * Separato and vedovo are part of the stato civile, shown by PersonaInNomadelfia::statoCivile().
     */
    public function isStatoCivile(): bool
    {
        return in_array($this->stato, [self::$mapNamesToDB['separato'], self::$mapNamesToDB['vedovo']], true);
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function attivo($query)
    {
        return $query->where('persone_stati.stato', 1);
    }
}
