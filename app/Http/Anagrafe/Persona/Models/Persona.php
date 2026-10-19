<?php

declare(strict_types=1);

namespace App\Anagrafe\Persona\Models;

use App\Biblioteca\Models\Prestito;
use App\Nomadelfia\Concerns\PersonaInNomadelfia;
use App\Anagrafe\Exceptions\PersonaHasMultipleFamigliaAttuale;
use App\Patente\Models\Patente;
use App\Traits\SortableTrait;
use Database\Factories\PersonaFactory;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\QueryBuilders\PersonaQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $data_decesso
 * @property string $data_nascita
 * @property string $cognome
 * @property string $nome
 * @property string $nominativo
 * @property string $sesso
 * @property string $numero_elenco
 * @property string $provincia_nascita
 * @property string $cf
 * @property string $biografia
 * @property string $origine
 */
class Persona extends Model
{
    use HasFactory;
    use PersonaInNomadelfia;
    use SoftDeletes;
    use SortableTrait;

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';

    protected $table = 'persone';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function newEloquentBuilder($query): PersonaQueryBuilder
    {
        return new PersonaQueryBuilder($query);
    }
    protected function nome(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: fn($value): array => ['nome' => ucwords(mb_strtolower((string) $value))]);
    }
    protected function cognome(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: fn($value): array => ['cognome' => ucwords(mb_strtolower((string) $value))]);
    }
    protected function nominativo(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn($value): string => ucwords(mb_strtolower((string) $value)), set: fn($value): array => ['nominativo' => ucwords(mb_strtolower((string) $value))]);
    }

    public function buildCompleteName(): string
    {
        $year = \Illuminate\Support\Facades\Date::createFromFormat('Y-m-d', $this->data_nascita)->year;

        return "($year) $this->nome  $this->cognome";
    }

    public function getInitialLetterOfCogonome()
    {
        return Str::substr($this->cognome, 0, 1);
    }

    public function isMaschio(): bool
    {
        return $this->sesso === 'M';
    }

    public function isDeceduta(): bool
    {
        return $this->data_decesso !== null;
    }

    public function isMaggiorenne(): bool
    {
        return \Illuminate\Support\Facades\Date::now()->diffInYears(\Illuminate\Support\Facades\Date::parse($this->data_nascita), true) > 18;
    }

    // *************
    //  RELATIONSHIPS
    // *************

    public function patenti(): HasMany
    {
        return $this->hasMany(Patente::class, 'persona_id', 'id');
    }

    public function prestiti()
    {
        return $this->hasMany(Prestito::class, 'cliente_id', 'id');
    }

    public function nominativiStorici(): HasOne
    {
        return $this->hasOne(NominativoStorico::class, 'persona_id', 'id');
    }

    public function famiglie(): BelongsToMany
    {
        return $this->belongsToMany(Famiglia::class, 'famiglie_persone', 'persona_id', 'famiglia_id')
            ->withPivot('stato');
    }

    public function famigliaAttuale()
    {
        $famiglia = $this->famiglie()
            ->wherePivot('stato', '1')
            ->withPivot('posizione_famiglia')
            ->get();
        if ($famiglia->count() === 0) {
            // IF null; the person has no a family so it is a single
            return null;
        }
        if ($famiglia->count() === 1) {
            return $famiglia[0];
        }
        throw PersonaHasMultipleFamigliaAttuale::named($this->nominativo);
    }

    public function famigliaPosizione(string $posizione): bool
    {
        if ($this->famigliaAttuale()) {
            return $this->famigliaAttuale()->pivot->posizione_famiglia === $posizione;
        }

        return false;
    }

    public function isCapoFamiglia(): bool
    {
        return $this->famigliaPosizione('CAPO FAMIGLIA');
    }

    public function isMoglie(): bool
    {
        return $this->famigliaPosizione('MOGLIE');
    }

    public function isFiglio(): bool
    {
        return $this->isFiglioNato() or $this->isFiglioAccolto();
    }

    public function isFiglioNato(): bool
    {
        return $this->famigliaPosizione('FIGLIO NATO');
    }

    public function isFiglioAccolto(): bool
    {
        return $this->famigliaPosizione('FIGLIO ACCOLTO');
    }

    public function famiglieStorico(): BelongsToMany
    {
        return $this->famiglie()
            ->wherePivot('stato', '0')
            ->withPivot('posizione_famiglia');
    }

    protected static function newFactory()
    {
        return PersonaFactory::new();
    }
}
