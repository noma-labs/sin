<?php

declare(strict_types=1);

namespace App\Nomadelfia\EserciziSpirituali\Models;

use App\Nomadelfia\Exceptions\EsSpiritualeNotActive;
use App\Nomadelfia\Persona\Models\Persona;
use Database\Factories\EsSpiritualiFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * @property string $stato
 * @property string $turno
 */
final class EserciziSpirituali extends Model
{
    use HasFactory;

    protected $connection = 'db_nomadelfia';

    protected $table = 'esercizi_spirituali';

    protected $primaryKey = 'id';

    protected $guarded = [''];

    /*
    *  Ritorna le personemaggiorenne interne che nono sono in nessun es spirituale
    *
    */
    public static function personeNoEsercizi(): stdClass
    {
        $expression = DB::raw("
            SELECT persone.*
            FROM persone
            INNER JOIN popolazione ON popolazione.persona_id = persone.id
            WHERE persone.id NOT IN (
                      SELECT persone_esercizi.persona_id
                      FROM persone_esercizi
                      INNER JOIN esercizi_spirituali ON esercizi_spirituali.id = persone_esercizi.esercizi_id
                      where esercizi_spirituali.stato = '1'
            )  AND popolazione.data_uscita IS NULL  AND persone.data_nascita <= DATE_SUB(NOW(), INTERVAL 18 YEAR)
            ORDER BY persone.nominativo");
        $persone = DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
        );
        $result = new stdClass;
        $per = collect($persone);
        $sesso = $per->groupBy('sesso');
        $result->total = $per->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    /**
     * Returns gli esercizi spirituali attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('stato', '=', '1');
    }

    public function isAttivo(): bool
    {
        return $this->stato === '1';
    }

    public function persone()
    {
        return $this->belongsToMany(Persona::class, 'persone_esercizi', 'esercizi_id', 'persona_id');
    }

    public function personeOk($orderby = 'data_nascita'): stdClass
    {
        $expression = DB::raw("SELECT persone.* , esercizi_spirituali.id as esercizi_id
              FROM persone
              INNER JOIN persone_esercizi ON persone_esercizi.persona_id = persone.id
              INNER JOIN esercizi_spirituali ON esercizi_spirituali.id = persone_esercizi.esercizi_id
              WHERE esercizi_spirituali.stato = '1' AND esercizi_spirituali.id =:id
              ORDER BY persone.nominativo");
        $persone = DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['id' => $this->id]
        );
        $result = new stdClass;
        $per = collect($persone);
        $sesso = $per->groupBy('sesso');
        $result->total = $per->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    public function aggiungiPersona($persona)
    {
        if (! $this->isAttivo()) {
            throw EsSpiritualeNotActive::named($this);
        }
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->persone()->attach($persona->id);
        }
        throw new Exception('Bad person as arguemnt. It must be the id or the model of a person.');
    }

    public function eliminaPersona($persona)
    {
        if (! $this->isAttivo()) {
            throw EsSpiritualeNotActive::named($this);
        }
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->persone()->detach($persona->id);
        }
        throw new Exception('Bad person as arguemnt. It must be the id or the model of a person.');
    }

    public function responsabile()
    {
        return $this->belongsTo(Persona::class, 'responsabile_id', 'id');
    }

    public function assegnaResponsabile($persona)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->responsabile()->associate($persona);
        }
        throw new Exception('Bad person as arguemnt. It must be the id or the model of a person.');
    }

    protected static function newFactory()
    {
        return EsSpiritualiFactory::new();
    }
}
