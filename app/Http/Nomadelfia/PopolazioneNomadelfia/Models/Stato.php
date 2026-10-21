<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Exceptions\StatoDoesNotExists;
use Illuminate\Database\Eloquent\Model;

/**
 * A stato of a person that cannot be derived from the famiglia: sacerdote, mamma di vocazione, nomadelfa mamma, mamma nubile, separato,
 * and sposato or vedovo of a coniuge living outside Nomadelfia, who is not in the famiglia.
 * Otherwise the stato civile (celibe, nubile, sposato, vedovo) is derived by PersonaInNomadelfia::statoCivile().
 *
 * @property string $stato
 * @property string $nome
 */
final class Stato extends Model
{
    public $guarded = ['id'];

    public $timestamps = false;

    public static $mapNamesToDB = [
        'sacerdote' => 'SAC',
        'mammavocazione' => 'MAV',
        'nomadelfamamma' => 'MAM',
        'mammanubile' => 'MAN',
        'separato' => 'SEP',
        'sposatoconiugeesterno' => 'SPO',
        'vedovoconiugeesterno' => 'VED',
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
