<?php

namespace App\Scuola\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function scopeIsPrescuola(){
        return $this->nome == "Prescuola";
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
