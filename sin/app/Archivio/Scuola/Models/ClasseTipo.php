<?php

namespace App\Scuola\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClasseTipo extends Model
{

    const PRIMA_MEDIA = '1a media';
    const SECONDA_MEDIA = '2a media';
    const TERZA_MEDIA = '3a media';

    public $timestamps = true;

    protected $connection = 'db_scuola';
    protected $table = 'tipo';
    protected $primaryKey = "id";


    public function alunni()
    {
        return $this->belongsToMany(Persona::class, 'persona_classi', 'classe_id',
            'persona_id')->withPivot('data_inizio');
    }

    public function alunniAttuali()
    {
        return $this->alunni()->where('data_fine', "=", null);
    }

    public function scopeClasseSuccessiva($query)
    {
        // TODO: prossima classe usando il campo 'next'
        return $query->where('ciclo', '=', $this->ciclo)->where('ord', '>', $this->ord)
            ->orWhere(function ($query) {
                $query->where('ord', '>', $this->ord);
            })->orderBY('ord', 'asc')->first();
    }

    public function scopePrescuola($query)
    {
        return $query->where('ciclo', '=', 'prescuola')->first();
    }

    public function scopeElementari($query)
    {
        return $query->where('ciclo', '=', 'elementari');
    }

    public function scopePrimaElem($query)
    {
        return $query->where('nome', '=', '1a Elementare')->first();
    }

    public function scopeSecondaElem($query)
    {
        return $query->where('nome', '=', '2a Elementare')->first();
    }

    public function scopeTerzaElem($query)
    {
        return $query->where('nome', '=', '3a Elementare')->first();
    }

    public function scopeQuartaElem($query)
    {
        return $query->where('nome', '=', '4a Elementare')->first();
    }

    public function scopeQuintaElem($query)
    {
        return $query->where('nome', '=', '5a Elementare')->first();
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
        return $this->ciclo === "prescuola";
    }

    public function scopeIsElementari(): bool
    {
        return $this->ciclo === "elementari";
    }

    public function scopeIsMedie(): bool
    {
        return $this->ciclo === "medie";
    }

    public function scopeIsSuperiori(): bool
    {
        return $this->ciclo === "superiori";
    }

    public function scopeIsPrimaEl()
    {
        return $this->nome == "1a Elementare";
    }

    public function scopeIsSecondaEl()
    {
        return $this->nome == "2a Elementare";
    }

    public function scopeIsTerzaEl()
    {
        return $this->nome == "3a Elementare";
    }

    public function scopeIsQuartaEl()
    {
        return $this->nome == "4a Elementare";
    }

    public function scopeIsQuintaEl()
    {
        return $this->nome == "5a Elementare";
    }

    public function IsPrimaMed()
    {
        return $this->nome == self::PRIMA_MEDIA;
    }

    public function scopeIsSecondaMed()
    {
        return $this->nome == self::SECONDA_MEDIA;
    }

    public function scopeIsTerzaMed()
    {
        return $this->nome == self::TERZA_MEDIA;
    }

    public function scopeIsUniversita(): bool
    {
        return $this->ciclo === 'universita';
    }

    public function assegnaAlunno($persona, Carbon\Carbon $data_inizio)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
//            DB::connection('db_nomadelfia')->beginTransaction();
//            try {
//                $attuale = $this->posizioneAttuale();
//                if ($attuale) {
//                    $this->posizioni()->updateExistingPivot($attuale->id,
//                        ['stato' => '0', 'data_fine' => ($attuale_data_fine ? $attuale_data_fine : $data_inizio)]);
//                }
            $this->alunni()->attach($persona->id, ['data_inizio' => $data_inizio]);
//                DB::connection('db_nomadelfia')->commit();
//            } catch (\Exception $e) {
//                DB::connection('db_nomadelfia')->rollback();
//                throw $e;
//            }
        } else {
            throw new Exception("Bad Argument. Persona must be an id or a model.");
        }
    }


}
