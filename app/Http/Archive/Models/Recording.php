<?php

declare(strict_types=1);

namespace App\Archive\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string|null $code
 * @property \Illuminate\Support\Carbon $DATA
 * @property string|null $ORE
 * @property string|null $LOCALITA
 * @property string|null $PR
 * @property string|null $AUTORE
 * @property string|null $ARGOMENTO
 * @property string|null $DESTINATARI
 * @property string|null $DOC
 * @property bool|null $RAC
 * @property bool|null $CART
 * @property bool|null $PDF_SCAN
 * @property bool|null $ORIG
 * @property bool|null $MS_DATT
 * @property int|null $MIN_PAG
 * @property string|null $T
 * @property string|null $TRASCRITT
 * @property string|null $CONTROLLO
 * @property string|null $GENERE
 * @property string|null $REPER
 * @property string|null $EDITO
 * @property string|null $MINIDISC
 * @property string|null $MDSEG
 * @property string|null $NUMER
 * @property string|null $QUALITA_AUDIO
 * @property string|null $PUBBLICABILE
 * @property string|null $IMPORTANZA_DESTINAT
 * @property string|null $ARGOMENTI
 * @property string|null $C
 * @property string|null $G
 * @property string|null $S
 * @property string|null $NOTE
 * @property string|null $sintesi_PF
 */
final class Recording extends Model
{
    public $timestamps = false;

    protected $connection = 'archivio_nomadelfia';

    protected $table = 'recordings';

    protected $guarded = [];

    protected $primaryKey = 'id';

    public function transcript(): HasOne
    {
        return $this->hasOne(RecordingTranscript::class, 'recording_id');
    }

    protected static function booted(): void
    {
        self::addGlobalScope('before_1981', fn (Builder $query) => $query->where('data', '<=', '1981-01-15'));
    }

    protected function casts(): array
    {
        return [
            'DATA' => 'date',
            'RAC' => 'boolean',
            'CART' => 'boolean',
            'PDF_SCAN' => 'boolean',
            'ORIG' => 'boolean',
            'MS_DATT' => 'boolean',
        ];
    }
}
