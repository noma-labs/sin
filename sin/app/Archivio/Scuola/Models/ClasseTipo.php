<?php

namespace App\Scuola\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClasseTipo extends Model
{

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
        return $this->alunni()->wherePivotIn('stato', ['Attivo', 'Sospeso'])->orderBy('mansione', 'asc');
    }

    public function scopePrescuola($query)
    {
        return $query->where('ciclo', '=','prescuola');
    }

    public function scopeElementari($query)
    {
        return $query->where('ciclo',  '=', 'elementari');
    }

    public function scopeMedie($query)
    {
        return $query->where('ciclo', '=', 'medie');
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

    public function scopeIsPrescuola3Anni()
    {
        return Str::contains($this->nome, '3 Anni');
    }

    public function scopeIsPrescuola4Anni()
    {
        return Str::contains($this->nome, '4 Anni');

    }

    public function scopeIsPrescuola5Anni()
    {
        return Str::contains($this->nome, '5 Anni');

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

    public function scopeIsPrimaMed()
    {
        return $this->nome == "1a Media";
    }

    public function scopeIsSecondaMed()
    {
        return $this->nome == "2a Media";
    }

    public function scopeIsTerzaMed()
    {
        return $this->nome == "3a Media";
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
