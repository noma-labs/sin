<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Exceptions\StatoDoesNotExists;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * A stato of a person that cannot be derived from the famiglia: sacerdote, separato.
 * The stato civile (celibe, nubile, sposato, vedovo) is derived by PersonaInNomadelfia::statoCivile(),
 * mamma di vocazione, nomadelfa mamma and mamma nubile by the tipo of the famiglia the person heads (TipoFamiglia).
 * The old rows MAV, MAM and MAN are still in the tables stati and persone_stati, but are ignored.
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
    ];

    protected $connection = 'db_nomadelfia';

    protected $table = 'stati';

    protected $primaryKey = 'id';

    /**
     * The stati a person can have, without the old stati replaced by the tipo of the famiglia.
     *
     * @return Collection<int, self>
     */
    public static function assegnabili(): Collection
    {
        return self::all()->whereIn('stato', self::$mapNamesToDB)->sortBy('nome')->values();
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

    public function isSeparato(): bool
    {
        return $this->stato === self::$mapNamesToDB['separato'];
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function attivo($query)
    {
        return $query->where('persone_stati.stato', 1);
    }
}
