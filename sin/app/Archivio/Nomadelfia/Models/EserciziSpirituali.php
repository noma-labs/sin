<?php

namespace App\Nomadelfia\Models;

use \stdClass;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Famiglia;

use App\Nomadelfia\Exceptions\EsSpiritualeNotActive;

class EserciziSpirituali extends Model
{
    protected $connection = 'db_nomadelfia';
    protected $table = 'esercizi_spirituali';
    protected $primaryKey = "id";

    protected $guarded = [''];

    /**
     * Returns gli esercizi spirituali attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('stato', "=", '1');
    }

    public function isAttivo()
    {
        return $this->stato == "1";
    }

    public function persone()
    {
        return $this->belongsToMany(Persona::class, 'persone_esercizi', 'esercizi_id', 'persona_id');
    }

    public function personeOk($orderby='data_nascita')
    {
        $persone =  DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT persone.* , esercizi_spirituali.id as esercizi_id
              FROM persone
              INNER JOIN persone_esercizi ON persone_esercizi.persona_id = persone.id
              INNER JOIN esercizi_spirituali ON esercizi_spirituali.id = persone_esercizi.esercizi_id
              WHERE esercizi_spirituali.stato = '1' AND esercizi_spirituali.id =:id
              ORDER BY persone.nominativo"),
            array("id"=>$this->id)
        );
        $result = new stdClass;
        $per = collect($persone);
        $sesso = $per->groupBy("sesso");
        $result->total =  $per->count();
        $result->uomini =  $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }

    public function aggiungiPersona($persona)
    {
        if (!$this->isAttivo()) {
            throw EsSpiritualeNotActive::named($this);
        }
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->persone()->attach($persona->id);
        }
        throw Exception("Bad person as arguemnt. It must be the id or the model of a person.");
    }

    public function eliminaPersona($persona)
    {
        if (!$this->isAttivo()) {
            throw EsSpiritualeNotActive::named($this);
        }
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->persone()->detach($persona->id);
        }
        throw Exception("Bad person as arguemnt. It must be the id or the model of a person.");
    }

    public function responsabile()
    {
        return $this->hasOne(Persona::class, "id", "responsabile_id");
    }

    public function assegnaResponsabile($persona)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return  $this->responsabile()->save($persona);
        }
        return $this->hasOne(Persona::class, "persona_id", "id");
    }
}