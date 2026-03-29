<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use App\Nomadelfia\Persona\Models\Persona;
use App\Scuola\Exceptions\GeneralException;
use Carbon\Carbon;
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

    public function IsPrimaMed(): bool
    {
        return $this->nome === self::PRIMA_MEDIA;
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

    /**
     * @return Builder<ClasseTipo>
     */
    protected function scopeClasseSuccessiva($query)
    {
        return $query->where('id', '=', $this->next);
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function prescuola($query): self
    {
        return $query->where('ciclo', '=', 'prescuola')->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function elementari($query)
    {
        return $query->where('ciclo', '=', 'elementari');
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function anni3Prescuola($query)
    {
        return $query->where('nome', '=', self::PRESCUOLA_3ANNI)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function anni4Prescuola($query)
    {
        return $query->where('nome', '=', self::PRESCUOLA_4ANNI)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function anni5Prescuola($query)
    {
        return $query->where('nome', '=', self::PRESCUOLA_5ANNI)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function primaElem($query)
    {
        return $query->where('nome', '=', self::PRIMA_ELEMENTARE)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function secondaElem($query)
    {
        return $query->where('nome', '=', self::SECONDA_ELEMENTARE)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function terzaElem($query)
    {
        return $query->where('nome', '=', self::TERZA_ELEMENTARE)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function quartaElem($query)
    {
        return $query->where('nome', '=', self::QUARTA_ELEMENTARE)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function quintaElem($query)
    {
        return $query->where('nome', '=', self::QUINTA_ELEMENTARE)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function medie($query)
    {
        return $query->where('ciclo', '=', 'medie');
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function primaMed($query)
    {
        return $query->where('nome', '=', self::PRIMA_MEDIA)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function secondaMed($query)
    {
        return $query->where('nome', '=', self::SECONDA_MEDIA)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function terzaMed($query)
    {
        return $query->where('nome', '=', self::TERZA_MEDIA)->first();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function superiori($query)
    {
        return $query->where('ciclo', '=', 'superiori');
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isPrescuola(): bool
    {
        return $this->ciclo === 'prescuola';
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isElementari(): bool
    {
        return $this->ciclo === 'elementari';
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isMedie(): bool
    {
        return $this->ciclo === 'medie';
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isSuperiori(): bool
    {
        return $this->ciclo === 'superiori';
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function is3AnniPrescuola($query): bool
    {
        return $this->nome === self::PRESCUOLA_3ANNI;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function is4AnniPrescuola($query): bool
    {
        return $this->nome === self::PRESCUOLA_4ANNI;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function is5AnniPrescuola($query): bool
    {
        return $this->nome === self::PRESCUOLA_5ANNI;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isPrimaEl(): bool
    {
        return $this->nome === self::PRIMA_ELEMENTARE;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isSecondaEl(): bool
    {
        return $this->nome === self::SECONDA_ELEMENTARE;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isTerzaEl(): bool
    {
        return $this->nome === self::TERZA_ELEMENTARE;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isQuartaEl(): bool
    {
        return $this->nome === self::QUARTA_ELEMENTARE;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isQuintaEl(): bool
    {
        return $this->nome === self::QUINTA_ELEMENTARE;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isSecondaMed(): bool
    {
        return $this->nome === self::SECONDA_MEDIA;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isTerzaMed(): bool
    {
        return $this->nome === self::TERZA_MEDIA;
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function isUniversita(): bool
    {
        return $this->ciclo === 'universita';
    }
}
