<?php

namespace Domain\Nomadelfia\Famiglia\Models;

use App\Nomadelfia\Exceptions\CouldNotAssignCapoFamiglia;
use App\Nomadelfia\Exceptions\CouldNotAssignMoglie;
use App\Nomadelfia\Exceptions\FamigliaHasNoGroup;
use App\Nomadelfia\Exceptions\PersonaHasMultipleGroup;
use App\Traits\Enums;
use Carbon;
use Database\Factories\FamigliaFactory;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaDaNomadelfiaAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Famiglia extends Model
{
    use Enums;
    use HasFactory;

    protected $connection = 'db_nomadelfia';

    protected $table = 'famiglie';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected static function newFactory()
    {
        return FamigliaFactory::new();
    }

    protected $enumPosizione = [
        'CAPO FAMIGLIA',
        'MOGLIE',
        'FIGLIO NATO',
        'FIGLIO ACCOLTO',
        'SINGLE',
    ];

    /**
     * Set the nome in uppercase when a new famiglia is insereted.
     */
    public function setNomeFamigliaAttribute($value)
    {
        $this->attributes['nome_famiglia'] = ucwords(strtolower($value));
    }

    public static function getCapoFamigliaEnum()
    {
        return self::getEnum('Posizione')[0];
    }

    public static function getMoglieEnum()
    {
        return self::getEnum('Posizione')[1];
    }

    public static function getFiglioNatoEnum()
    {
        return self::getEnum('Posizione')[2];
    }

    public static function getFiglioAccoltoEnum()
    {
        return self::getEnum('Posizione')[3];
    }

    public static function getSingleEnum()
    {
        return self::getEnum('Posizione')[4];
    }

    public static function figliEnums()
    {
        return collect(self::getEnum('Posizione'))->filter(function ($value, $key) {
            return Str::startsWith($value, 'FIGLIO');
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('nome_famiglia', 'asc')->get();
    }

    /**
     * Returns the families with a CAPO FAMIGLIA (men or women)
     */
    public static function conCapofamiglia()
    {
        return DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT famiglie.*
              FROM `famiglie` 
              INNER JOIN famiglie_persone on famiglie_persone.famiglia_id = famiglie.id
              WHERE famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
              ORDER BY famiglie.nome_famiglia")
        );
    }

    public function scopeFamigliePerPosizioni($query, $posizione, $stato = '1')
    {
        $q = $query->select('famiglie.*', 'persone.sesso', 'famiglie_persone.posizione_famiglia',
            'famiglie_persone.stato')
            ->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'famiglie.id')
            ->join('persone', 'famiglie_persone.persona_id', '=', 'persone.id')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->whereNull('popolazione.data_uscita')
            ->where('posizione_famiglia', $posizione)
            ->where('famiglie_persone.stato', $stato)
            ->orderBy('famiglie.nome_famiglia');

        return $q;
    }

    /**
     * Ritorna le famiglie che hanno come capo famiglia un maschio
     *
     * @author Davide Neri
     **/
    public function scopeMaschio($query)
    {
        return $query->where('sesso', 'M');
    }

    /**
     * Ritorna le famiglie che hanno come capo famiglia una femmina
     *
     * @author Davide Neri
     **/
    public function scopeFemmina($query)
    {
        return $query->where('sesso', 'F');
    }

    /**
     * Ritorna tutti capi famiglia delle famiglie
     *
     * @author Davide Neri
     **/
    public static function OnlyCapofamiglia()
    {
        return self::FamigliePerPosizioni('CAPO FAMIGLIA');
    }

    /**
     * Ritorna tutti i single delle famiglie
     *
     * @author Davide Neri
     **/
    public static function OnlySingle()
    {
        return self::FamigliePerPosizioni('SINGLE', '1');
    }

    /**
     * Uscita di una famiglia da Nomadelfia.
     * Esegure la funzione di uscita su tutti componenti ATTIVI della famiglia.
     *
     * @author Davide Neri
     **/
    public function uscita($data_uscita)
    {
        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $this->componentiAttuali()->get()->each(function ($componente) use ($data_uscita) {
                $act = app(UscitaDaNomadelfiaAction::class);
                $act->execute($componente, $data_uscita);
            });
            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }

    /**
     * Ritorna il gruppo familiare attuale in cui vive il CAPO FAMIGLIA o il SINGLE della famiglia.
     * Si assume che tutta la famiglia vive nello stesso gruppo del CAPO FAMIGLIA o SINGLE.
     *
     * @author Davide Neri
     **/
    public function gruppoFamiliareAttuale()
    {
        $res = collect(DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT gruppi_familiari.*, gruppi_persone.data_entrata_gruppo
            FROM famiglie_persone
            INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
            INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
            WHERE (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA' or famiglie_persone.posizione_famiglia = 'SINGLE')
                and famiglie_persone.famiglia_id = :famiglia_id and gruppi_persone.stato = '1' and famiglie_persone.stato = '1'"),
            ['famiglia_id' => $this->id]
        ));
        if ($res->count() == 1) {
            return $res->first();
        } elseif ($res->count() == 0) {
            return null;
        } else {
            throw PersonaHasMultipleGroup::named($this->nome_famiglia);
        }
    }

    public function gruppoFamiliareAttualeOrFail()
    {
        $gruppo = $this->gruppoFamiliareAttuale();
        if ($gruppo == null) {
            throw FamigliaHasNoGroup::named($this->nome_famiglia);
        }

        return $gruppo;
    }

    /**
     * Ritorna i gruppi familiari storici in cui ha vissuto il CAPO FAMIGLIA o il SINGLE della famiglia
     *
     * @author Davide Neri
     **/
    public function gruppiFamiliariStorico()
    {
        $res = DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT gruppi_familiari.*, gruppi_persone.data_entrata_gruppo, gruppi_persone.data_uscita_gruppo
      FROM famiglie_persone
      INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
      INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
      WHERE (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA' or famiglie_persone.posizione_famiglia = 'SINGLE')
       and famiglie_persone.famiglia_id = :famiglia_id and gruppi_persone.stato = '0'"),
            ['famiglia_id' => $this->id]
        );

        return $res;
    }

    public function mycomponenti()
    {
        $res = DB::connection('db_nomadelfia')->select(
            DB::raw('SELECT famiglie.id, famiglie_persone.*, persone.id, persone.nominativo, persone.data_nascita  
                    FROM famiglie 
                    INNER JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id 
                    INNER JOIN persone ON persone.id = famiglie_persone.persona_id 
                    WHERE famiglie.id = :famiglia
                    ORDER BY persone.data_nascita, famiglie_persone.posizione_famiglia'),
            ['famiglia' => $this->id]
        );

        return $res;
    }

    /**
     * Aggiunge il capo famiglia alla famiglia
     *
     * @author Davide Neri
     **/
    public function assegnaCapoFamiglia($persona, string $data_entrata = null, $note = null)
    {

        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $capo = $this->capofamiglia();
            if ($capo != null) {
                throw CouldNotAssignCapoFamiglia::hasAlreadyCapoFamiglia($this, $capo);
            }
            $single = $this->single();
            if ($single != null) {
                throw CouldNotAssignCapoFamiglia::beacuseIsSingle($this, $single);
            }
            if ($persona->isMaggiorenne() != true) {
                throw CouldNotAssignCapoFamiglia::beacuseIsMinorenne($this, $persona);
            }
            $data = $data_entrata ? $data_entrata : $this->data_creazione;

            return $this->assegnaComponente($persona, $this->getCapoFamigliaEnum(), $data);
        } else {
            throw new InvalidArgumentException("Identiticativo `{$persona}` della persona non valido.");
        }
    }

    /**
     * Aggiunge la moglie alla famiglia
     *
     * @author Davide Neri
     **/
    public function assegnaMoglie($persona, $data = null, $note = null)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            if ($this->moglie() != null) {
                throw CouldNotAssignMoglie::hasAlreadyMoglie($this, $persona);
            }
            $single = $this->single();
            if ($single != null) {
                throw CouldNotAssignMoglie::beacuseIsSingle($this, $persona);
            }
            if ($persona->isMaggiorenne() == false) {
                throw CouldNotAssignMoglie::beacuseIsMinorenne($this, $persona);
            }
            if ($persona->isMaschio() == true) {
                throw CouldNotAssignMoglie::beacuseIsMan($this, $persona);
            }
            $data = $data ? $data : $this->data_creazione;

            return $this->assegnaComponente($persona, $this->getMoglieEnum(), $data);
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    public function assegnaSingle($persona, $data = null, $note = null)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $data = $data ? $data : Carbon::parse($this->nascita)->addYears(18)->toDateString();

            return $this->assegnaComponente($persona, $this->getSingleEnum(), $data);
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    /**
     * Aggiunge un figlio nato nella famiglia
     *
     * @author Davide Neri
     **/
    public function assegnaFiglioNato($persona, $note = null)
    {
        // TODO: check the la persona non ha già una famiglia associata
        // TODO: check that the family is not a SINGLE
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->assegnaComponente($persona, $this->getFiglioNatoEnum(), $persona->data_nascita);
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    /**
     * Aggiunge un figlio accolto nella famiglia
     *
     * @author Davide Neri
     **/
    public function assegnaFiglioAccolto($persona, $data_accolto, $note = null)
    {
        // TODO: check the la persona non ha già una famiglia associata
        // TODO: check that the family is not a SINLGE
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->assegnaComponente($persona, $this->getFiglioAccoltoEnum(), $data_accolto);
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    public function assegnaComponente($persona, string $posizione, string $data_entrata, string $stato = '1', $note = null)
    {
        if (!in_array($posizione, $this->enumPosizione)) {
            throw new InvalidArgumentException("La posizione `{$posizione}` è invalida");
        }

        return $this->componenti()->attach($persona->id,
            ['stato' => $stato, 'posizione_famiglia' => $posizione, 'data_entrata' => $data_entrata, 'note' => $note]);
    }

    /**
     * Fa uscire un figlio dal nucleo familiare.
     * Il figlio rimane nucleo familiare come "fuori nucleo familiare".
     *
     * @author Davide Neri
     **/
    public function uscitaDalNucleoFamiliare($persona, $data_uscita, $note = null)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->componenti()->updateExistingPivot($persona->id,
                ['stato' => '0', 'data_uscita' => $data_uscita, 'note' => $note]);
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    /**
     * @Depreacted
     * Use the mycomponenti()
     * Ritorna i componenti che hanno fatto parte della famiglia (padre, madre, e figli)
     *
     * @author Davide Neri
     **/
    public function componenti()
    {
        return $this->belongsToMany(Persona::class, 'famiglie_persone', 'famiglia_id', 'persona_id')
            ->withPivot('stato', 'posizione_famiglia', 'data_entrata', 'data_uscita')
            ->orderby('nominativo');
    }

    /**
     * Ritorna i componenti attuali della famiglia (padre, madre, e figli)
     *
     * @author Davide Neri
     **/
    public function componentiAttuali()
    {
        return $this->componenti()->where('famiglie_persone.stato', '1');
    }

    /**
     * Ritorna il capofamiglia della famiglia.
     *
     * @author Davide Neri
     **/
    public function capofamiglia()
    {
        return $this->componenti()
            ->withPivot('stato', 'posizione_famiglia', 'data_entrata', 'data_uscita')
            ->wherePivot('posizione_famiglia', 'CAPO FAMIGLIA')
            ->first();
    }

    /**
     * Ritorna la persona single della famiglia.
     *
     * @author Davide Neri
     **/
    public function single()
    {
        return $this->componenti()
            ->wherePivot('posizione_famiglia', 'SINGLE')
            ->first();
    }

    /**
     * Ritorna la moglie della famiglia.
     *
     * @author Davide Neri
     **/
    public function moglie()
    {
        return $this->componenti()
            ->wherePivot('posizione_famiglia', 'MOGLIE')
            ->first();
    }

    /**
     * Ritorna i figli  (sia nati che accolti) della famiglia.
     *
     * @author Davide Neri
     **/
    public function figli()
    {
        return $this->belongsToMany(Persona::class, 'famiglie_persone', 'famiglia_id', 'persona_id')
            ->withPivot('stato', 'posizione_famiglia', 'data_entrata', 'data_uscita')
            ->wherePivotIn('posizione_famiglia', ['FIGLIO NATO', 'FIGLIO ACCOLTO'])
            ->orderBy('data_nascita');
    }

    /**
     * Ritorna i figli attuali (sia nati che accolti) della famiglia.
     *
     * @author Davide Neri
     **/
    public function figliAttuali()
    {
        return $this->figli()->wherePivot('stato', '=', '1');
    }

    /**
     * Rimuove tutti i componenti della famiglia da un gruppo familiare
     *
     * @author Davide Neri
     **/
    public function rimuoviDaGruppoFamiliare($idGruppo)
    {
        DB::connection('db_nomadelfia')->update(
            DB::raw("UPDATE gruppi_persone
              SET
                  gruppi_persone.stato = '0'
              WHERE
                gruppi_persone.gruppo_famigliare_id = :gruppoattuale
                AND gruppi_persone.persona_id IN (
                      SELECT persone.id
                      FROM famiglie_persone
                      INNER JOIN persone ON persone.id = famiglie_persone.persona_id
                      #INNER join gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
                      WHERE famiglie_persone.famiglia_id = :famigliaId  AND famiglie_persone.stato = '1' #AND gruppi_persone.stato = '1'
                )
                AND gruppi_persone.stato = '1' "),
            ['gruppoattuale' => $idGruppo, 'famigliaId' => $this->id]
        );
    }

    public static function famiglieNumerose(int $min_componenti = 5)
    {
        return DB::connection('db_nomadelfia')->select(
            DB::raw("WITH famiglie_numerose AS (
                             SELECT f.id, count(*) as componenti
                             FROM `famiglie`  f
                             INNER JOIN famiglie_persone fp ON fp.famiglia_id = f.id
                             INNER JOIN popolazione pop ON pop.persona_id = fp.persona_id
                             WHERE fp.stato = '1' and pop.data_uscita is NULL
                             GROUP BY f.id
                             HAVING componenti >= :minc 
                             ORDER BY componenti DESC
                    ) select ff.*, famiglie_numerose.componenti
                    from famiglie_numerose
                    join famiglie ff on famiglie_numerose.id = ff.id
                    order by famiglie_numerose.componenti DESC;"), ['minc' => $min_componenti]
        );
    }

    /**
     * Assegna un nuovo gruppo familiare alla famiglia.
     *
     * @author Davide Neri
     **/
    public function assegnaFamigliaANuovoGruppoFamiliare(
        $gruppo_attuale_id,
        $dataUscitaGruppoFamiliareAttuale,
        $gruppo_nuovo_id,
        $data_entrata = null
    )
    {
        $famiglia_id = $this->id;

        return DB::transaction(function () use (
            &$gruppo_attuale_id,
            $dataUscitaGruppoFamiliareAttuale,
            &$famiglia_id,
            &$gruppo_nuovo_id,
            &$data_entrata
        ) {

            // Disabilita tutti i componenti della famiglia dal vecchio gruppo (mette stato = 0)
            DB::connection('db_nomadelfia')->update(
                DB::raw("UPDATE gruppi_persone
              SET
                  gruppi_persone.stato = '0', data_uscita_gruppo = :data_uscita
              WHERE
                gruppi_persone.gruppo_famigliare_id = :gruppoattuale
                AND gruppi_persone.persona_id IN (
                      SELECT persone.id
                      FROM famiglie_persone
                      INNER JOIN persone ON persone.id = famiglie_persone.persona_id
                      #INNER join gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
                      WHERE famiglie_persone.famiglia_id = :famigliaId  AND famiglie_persone.stato = '1' #AND gruppi_persone.stato = '1'
                )
                    
                AND gruppi_persone.stato = '1' "),
                [
                    'gruppoattuale' => $gruppo_attuale_id,
                    'famigliaId' => $famiglia_id,
                    'data_uscita' => $dataUscitaGruppoFamiliareAttuale,
                ]
            );

            // Aggiungi a tutti i componenti della famiglia nel nuovo gruppo
            DB::connection('db_nomadelfia')->update(
                DB::raw("INSERT INTO gruppi_persone (persona_id, gruppo_famigliare_id, stato, data_entrata_gruppo)
              SELECT persone.id, :gruppo_nuovo_id, '1', :data_entrata
              FROM famiglie_persone
              INNER JOIN persone ON persone.id = famiglie_persone.persona_id
              WHERE famiglie_persone.famiglia_id = :famigliaId   AND famiglie_persone.stato = '1' "),
                [
                    'famigliaId' => $famiglia_id,
                    'gruppo_nuovo_id' => $gruppo_nuovo_id,
                    'data_entrata' => $data_entrata,
                ]// , 'data_uscita'=>$dataUscitaGruppoFamiliareAttuale)
            );

            return true;
        });
    }

    /*
    *  Ritorna le famiglie che hanno un errore nei loro componenti
    *   1) ci soono più di un SINGLE nella famiglia
    *   2) ci sono più CAPO FAMIGLIA attivi nella famiglia
    *   3) ci sono più di una MOGLIE nella famiglia
    * Ritonra le famiglie senza componenti
    */
    public static function famigliaConErrore()
    {
        $result = collect();
        $famiglie = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT famiglie.id, famiglie.nome_famiglia
              from (
                  SELECT famiglie_persone.famiglia_id, famiglie_persone.posizione_famiglia, count(*) as count
                  FROM famiglie_persone
                  WHERE famiglie_persone.stato = '1'
                  GROUP BY famiglie_persone.famiglia_id, famiglie_persone.posizione_famiglia
              ) AS g 
              INNER JOIN famiglie ON famiglie.id = g.famiglia_id
              WHERE (g.posizione_famiglia = 'SINGLE' AND g.count>1) OR   (g.posizione_famiglia = 'CAPO FAMIGLIA' AND g.count>1) OR (g.posizione_famiglia = 'MOGLIE' AND g.count>1)"
            )
        );
        $result->push((object)['descrizione' => 'Famiglie non valide', 'results' => $famiglie]);

        $famiglieSenzaComponenti = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT *
              FROM famiglie
              WHERE famiglie.id NOT IN (
              SELECT famiglie_persone.famiglia_id
                FROm famiglie_persone
                WHERE famiglie_persone.stato = '1'
                GROUP BY famiglie_persone.famiglia_id
              )"
            )
        );

        $result->push((object)[
            'descrizione' => 'Famiglie senza componenti o con nessun componente attivo',
            'results' => $famiglieSenzaComponenti,
        ]);

        $famiglieSenzaCapo = DB::connection('db_nomadelfia')->select(
            DB::raw("
      SELECT famiglie.*
      FROM  famiglie 
      WHERE famiglie.id NOT IN (
           SELECT famiglie_persone.famiglia_id
           FROM famiglie_persone
           WHERE famiglie_persone.stato = '1' AND (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA' OR famiglie_persone.posizione_famiglia = 'SINGLE')
      )
        ")
        );
        $result->push((object)['descrizione' => 'Famiglie senza un CAPO FAMIGLIA', 'results' => $famiglieSenzaCapo]);

        $famiglieConPiuGruppi = DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT *
              from famiglie
              WHERE famiglie.id IN (
                SELECT famiglie_persone.famiglia_id
                FROM famiglie_persone
                INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
                INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
                WHERE (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA' or famiglie_persone.posizione_famiglia = 'SINGLE')  and gruppi_persone.stato = '1' and  famiglie_persone.stato = '1'
                GROUP BY famiglie_persone.famiglia_id
                HAVING count(*) > 1
              )")
        );
        $result->push((object)[
            'descrizione' => 'Famiglie assegnate in più di un grupo familiare',
            'results' => $famiglieConPiuGruppi,
        ]);

        return $result;
    }

    /*
    *  Ritorna le persone Interne che non hanno una famiglia attiva.
    *
    */
    public static function personeSenzaFamiglia()
    {
        $personeSenzaFam = DB::connection('db_nomadelfia')->select(
            DB::raw('
        SELECT persone.id, persone.nominativo
        FROM persone
        INNER JOIN popolazione ON popolazione.persona_id = persone.id
        WHERE persone.id NOT IN (
          SELECT famiglie_persone.persona_id
          FROM famiglie_persone
        )  AND popolazione.data_uscita IS NULL')
        );

        return $personeSenzaFam;
    }
}
