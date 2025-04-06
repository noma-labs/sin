<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use App\Scuola\Exceptions\GeneralException;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $nome
 * @property string $ciclo
 * @property int $ord
 * @property int $next
 *
 * @method Prescuola()
 * @method Builder<ClasseTipo> classeSuccessiva()
 * */
final class ClasseTipo extends Model
{
    public const PRESCUOLA_3ANNI = '3 anni';

    public const PRESCUOLA_4ANNI = '4 anni';

    public const PRESCUOLA_5ANNI = '5 anni';

    public const PRIMA_ELEMENTARE = '1a Elementare';

    public const SECONDA_ELEMENTARE = '2a Elementare';

    public const TERZA_ELEMENTARE = '3a Elementare';

    public const QUARTA_ELEMENTARE = '4a Elementare';

    public const QUINTA_ELEMENTARE = '5a Elementare';

    public const PRIMA_MEDIA = '1a media';

    public const SECONDA_MEDIA = '2a media';

    public const TERZA_MEDIA = '3a media';

    public $timestamps = true;

    protected $connection = 'db_scuola';

    protected $table = 'tipo';

    protected $primaryKey = 'id';

    public function alunni(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'persona_classi', 'classe_id',
            'persona_id')->withPivot('data_inizio');
    }

    public function alunniAttuali(): BelongsToMany
    {
        return $this->alunni()->where('data_fine', '=', null);
    }

    /**
     * @return Builder<ClasseTipo>
     */
    public function scopeClasseSuccessiva($query)
    {
        return $query->where('id', '=', $this->next);
    }

    public function scopePrescuola($query): self
    {
        return $query->where('ciclo', '=', 'prescuola')->first();
    }

    public function scopeElementari($query)
    {
        return $query->where('ciclo', '=', 'elementari');
    }

    public function scopeAnni3Prescuola($query)
    {
        return $query->where('nome', '=', self::PRESCUOLA_3ANNI)->first();
    }

    public function scopeAnni4Prescuola($query)
    {
        return $query->where('nome', '=', self::PRESCUOLA_4ANNI)->first();
    }

    public function scopeAnni5Prescuola($query)
    {
        return $query->where('nome', '=', self::PRESCUOLA_5ANNI)->first();
    }

    public function scopePrimaElem($query)
    {
        return $query->where('nome', '=', self::PRIMA_ELEMENTARE)->first();
    }

    public function scopeSecondaElem($query)
    {
        return $query->where('nome', '=', self::SECONDA_ELEMENTARE)->first();
    }

    public function scopeTerzaElem($query)
    {
        return $query->where('nome', '=', self::TERZA_ELEMENTARE)->first();
    }

    public function scopeQuartaElem($query)
    {
        return $query->where('nome', '=', self::QUARTA_ELEMENTARE)->first();
    }

    public function scopeQuintaElem($query)
    {
        return $query->where('nome', '=', self::QUINTA_ELEMENTARE)->first();
    }

    public function scopeMedie($query)
    {
        return $query->where('ciclo', '=', 'medie');
    }

    public function scopePrimaMed($query)
    {
        return $query->where('nome', '=', self::PRIMA_MEDIA)->first();
    }

    public function scopeSecondaMed($query)
    {
        return $query->where('nome', '=', self::SECONDA_MEDIA)->first();
    }

    public function scopeTerzaMed($query)
    {
        return $query->where('nome', '=', self::TERZA_MEDIA)->first();
    }

    public function scopeSuperiori($query)
    {
        return $query->where('ciclo', '=', 'superiori');
    }

    public function scopeIsPrescuola(): bool
    {
        return $this->ciclo === 'prescuola';
    }

    public function scopeIsElementari(): bool
    {
        return $this->ciclo === 'elementari';
    }

    public function scopeIsMedie(): bool
    {
        return $this->ciclo === 'medie';
    }

    public function scopeIsSuperiori(): bool
    {
        return $this->ciclo === 'superiori';
    }

    public function scopeIs3AnniPrescuola($query): bool
    {
        return $this->nome === self::PRESCUOLA_3ANNI;
    }

    public function scopeIs4AnniPrescuola($query): bool
    {
        return $this->nome === self::PRESCUOLA_4ANNI;
    }

    public function scopeIs5AnniPrescuola($query): bool
    {
        return $this->nome === self::PRESCUOLA_5ANNI;
    }

    public function scopeIsPrimaEl(): bool
    {
        return $this->nome === self::PRIMA_ELEMENTARE;
    }

    public function scopeIsSecondaEl(): bool
    {
        return $this->nome === self::SECONDA_ELEMENTARE;
    }

    public function scopeIsTerzaEl(): bool
    {
        return $this->nome === self::TERZA_ELEMENTARE;
    }

    public function scopeIsQuartaEl(): bool
    {
        return $this->nome === self::QUARTA_ELEMENTARE;
    }

    public function scopeIsQuintaEl(): bool
    {
        return $this->nome === self::QUINTA_ELEMENTARE;
    }

    public function IsPrimaMed(): bool
    {
        return $this->nome === self::PRIMA_MEDIA;
    }

    public function scopeIsSecondaMed(): bool
    {
        return $this->nome === self::SECONDA_MEDIA;
    }

    public function scopeIsTerzaMed(): bool
    {
        return $this->nome === self::TERZA_MEDIA;
    }

    public function scopeIsUniversita(): bool
    {
        return $this->ciclo === 'universita';
    }

    public function assegnaAlunno($persona, Carbon $data_inizio): void
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $this->alunni()->attach($persona->id, ['data_inizio' => $data_inizio]);
        } else {
            throw new GeneralException('Bad Argument. Persona must be an id or a model.');
        }
    }
}
