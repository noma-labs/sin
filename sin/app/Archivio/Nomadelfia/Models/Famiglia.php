<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Nomadelfia\Models\Categoria;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Traits\Enums;
use Illuminate\Support\Str;


use App\Nomadelfia\Exceptions\FamigliaHasNoGroup;

class Famiglia extends Model
{
    use Enums;

    protected $connection = 'db_nomadelfia';
    protected $table = 'famiglie';
    protected $primaryKey = "id";

    protected $guarded = [];

    protected $enumPosizione = [
    'CAPO FAMIGLIA',
    'MOGLIE',
    'FIGLIO NATO',
    'FIGLIO ACCOLTO',
    'SINGLE'
   ];

    public static function figliEnums()
    {
        return collect(self::getEnum('Posizione'))->filter(function ($value, $key) {
            return Str::startsWith($value, 'FIGLIO');
        });
        ;
    }

    /**
     * Set the nome in uppercase when a new famiglia is insereted.
    */
    public function setNomeFamigliaAttribute($value)
    {
        $this->attributes['nome_famiglia'] = strtoupper($value);
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
        return  DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT famiglie.*
              FROM `famiglie` 
              INNER JOIN famiglie_persone on famiglie_persone.famiglia_id = famiglie.id
              WHERE famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
              ORDER BY famiglie.nome_famiglia")
        );
    }


    public function scopeFamigliePerPosizioni($query, $posizione, $stato='1')
    {
        return $query->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'famiglie.id')
                    ->join('persone', 'famiglie_persone.persona_id', '=', 'persone.id')
                    ->select('famiglie.*', "persone.sesso", 'famiglie_persone.posizione_famiglia', 'famiglie_persone.stato')
                    ->where("posizione_famiglia", $posizione)
                    ->where("famiglie_persone.stato", $stato)
                    ->where("persone.stato", '1')
                    ->orderBy("famiglie.nome_famiglia");
        /* return  DB::connection('db_nomadelfia')->select("
            SELECT `famiglie`.*, `persone`.`sesso`, `famiglie_persone`.`posizione_famiglia`, `famiglie_persone`.`stato`
            FROM `famiglie`
            INNER JOIN `famiglie_persone` on `famiglie_persone`.`famiglia_id` = `famiglie`.`id`
            INNER JOIN `persone` on `famiglie_persone`.`persona_id` = `persone`.`id`
            WHERE `posizione_famiglia` = ? and `famiglie_persone`.`stato` = ? and `persone`.`stato` = '1'
            ORDER BY `famiglie`.`nome_famiglia` asc",[$posizione, $stato]);
            */
    }

    /**
    * Ritorna le famiglie che hanno come capo famiglia un maschio
    * @author Davide Neri
    **/
    public function scopeMaschio($query)
    {
        return $query->where("sesso", "M");
    }

    /**
    * Ritorna le famiglie che hanno come capo famiglia una femmina
    * @author Davide Neri
    **/
    public function scopeFemmina($query)
    {
        return $query->where("sesso", "F");
    }

    /**
    * Ritorna tutti capi famiglie delle famiglie
    * @author Davide Neri
    **/
    public static function OnlyCapofamiglia()
    {
        return self::FamigliePerPosizioni("CAPO FAMIGLIA");
    }

    /**
    * Ritorna tutti i signle  delle famiglie
    * @author Davide Neri
    **/
    public static function OnlySingle()
    {
        return self::FamigliePerPosizioni("SINGLE", '1');
    }

    /**
     * Uscita di una famiglia da Nomadelfia.
     * Esegure la funzione di uscita su tutti componenti ATTIVI della famiglia.
     * @author Davide Neri
     **/
    public function uscita($data_uscita)
    {
        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $this->componentiAttuali()->get()->each(function ($componente) use ($data_uscita) {
                $componente->uscita($data_uscita, false);
            });
            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }


    /**
     * Ritorna True se tutti i componenti nel nucleo familiare sono persone esterne.
     *
     * @author Davide Neri
     **/
    public function isUscita()
    {
        $interno = Categoria::perNome("interno");
        $res = DB::connection('db_nomadelfia')->select(
            // seleziona tutti componenti attivi della famiglia che sono interni.
            DB::raw("SELECT famiglie.id, famiglie_persone.*, persone.id, persone.nominativo, persone.data_nascita  
                    FROM famiglie 
                    INNER JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id 
                    INNER JOIN persone ON persone.id = famiglie_persone.persona_id 
                    INNER JOIN persone_categorie ON persone.id = famiglie_persone.persona_id 
                    WHERE famiglie.id = :famiglia AND persone_categorie.categoria_id = :interno AND persone_categorie.stato = '1' AND famiglie_persone = '1'
                    ORDER BY persone.data_nascita, famiglie_persone.posizione_famiglia"),
            array('famiglia' => $this->id, 'interno'=>$interno->id)
        );
        if (count($res) >= 1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Ritorna il gruppo familiare attuale in cui vive il CAPO FAMIGLIA o il SINGLE della famiglia.
     * Si assume che tutta la famiglia vive nello stesso gruppo del CAPO FAMIGLIA o SINGLE:
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
            array("famiglia_id"=> $this->id)
        ));
        if ($res->count() == 1) {
            return $res->first();
        } elseif ($res->count() == 0) {
            return null;
        } else {
            throw PersonaHasMultipleGroup::named($this->nominativo);
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
            array("famiglia_id"=> $this->id)
        );
        return $res;
    }

    public function mycomponenti()
    {
        $res  = DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT famiglie.id, famiglie_persone.*, persone.id, persone.nominativo, persone.data_nascita  
                    FROM famiglie 
                    INNER JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id 
                    INNER JOIN persone ON persone.id = famiglie_persone.persona_id 
                    WHERE famiglie.id = :famiglia
                    ORDER BY persone.data_nascita, famiglie_persone.posizione_famiglia"),
            array('famiglia' => $this->id)
        );
        return $res;
    }

    /**
    *
    * Aggiunge il capo famiglia alla famiglia
    *
    * @author Davide Neri
    **/
    public function assegnaCapoFamiglia($persona, $data, $note=null)
    {
        // TODO: check the the person is maggiorenne e che  un maschio
        // TODO: check that the family is not a SINLGE
        if ($this->capofamiglia() != null) {
            throw Exception("La famiglia $this->nome ha già un capo famiglia");
        }
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->componenti()->attach($persona->id, ['stato'=>'1', 'posizione_famiglia'=>"CAPO FAMIGLIA", 'data_entrata'=>$data]);
        }
        throw Exception("Bad person as arguemnt. It must be the id or the model of a person.");
    }

    /**
    *
    * Aggiunge la moglie alla famiglia
    *
    * @author Davide Neri
    **/
    public function assegnaMoglie($persona, $data, $note=null)
    {
        // TODO: check the the person is maggiorennee femmina
        // TODO: check that the family is not a SINLGE
        if ($this->moglie() != null) {
            throw Exception("La famiglia $this->nome ha già una moglie");
        }
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->componenti()->attach($persona->id, ['stato'=>'1', 'posizione_famiglia'=>"MOGLIE", 'data_entrata'=>$data]);
        }
        throw Exception("Bad person as arguemnt. It must be the id or the model of a person.");
    }

    /**
    *
    * Aggiunge un figlio nato nella famiglia
    *
    * @author Davide Neri
    **/
    public function assegnaFiglioNato($persona, $note=null)
    {
        // TODO: check the la persona non ha già una famiglia associata
        // TODO: check that the family is not a SINLGE
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->componenti()->attach($persona->id, ['stato'=>'1', 'posizione_famiglia'=>"FIGLIO NATO", 'data_entrata'=>$persona->data_nascita]);
        }
        throw Exception("Bad person as arguemnt. It must be the id or the model of a person.");
    }

    /**
    *
    * Aggiunge un figlio accolto nella famiglia
    *
    * @author Davide Neri
    **/
    public function assegnaFiglioAccolto($persona, $data_accolto, $note=null)
    {
        // TODO: check the la persona non ha già una famiglia associata
        // TODO: check that the family is not a SINLGE
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->componenti()->attach($persona->id, ['stato'=>'1', 'posizione_famiglia'=>"FIGLIO ACCOLTO", 'data_entrata'=>$data_accolto]);
        }
        throw Exception("Bad person as arguemnt. It must be the id or the model of a person.");
    }


    /**
    * Fa uscire un figlio dal nucleo familiare.
    * Il figlio rimane nucleo familiare come "fuori nucleo familiare".
    *
    * @author Davide Neri
    **/
    public function uscitaDalNucleoFamiliare($persona, $data_uscita, $note=null)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->componenti()->updateExistingPivot($persona->id, ['stato'=>'0','data_uscita'=>$data_uscita,'note'=>$note]);
        }
        throw Exception("Bad person as arguemnt. It must be the id or the model of a person.");
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
            ->withPivot("stato", 'posizione_famiglia', 'data_entrata', 'data_uscita')
            ->orderby("nominativo");
    }

    /**
    * Ritorna i componenti attuali della famiglia (padre, madre, e figli)
    * @author Davide Neri
    **/
    public function componentiAttuali()
    {
        return $this->componenti()->where("famiglie_persone.stato", '1');
    }

    /**
    * Ritorna il capofamiglia della famiglia.
    * @author Davide Neri
    **/
    public function capofamiglia()
    {
        return $this->componenti()
                ->wherePivot('posizione_famiglia', 'CAPO FAMIGLIA')
                ->first();
    }

    /**
    * Ritorna la persona single della famiglia.
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
    * @author Davide Neri
    **/
    public function figli()
    {
        return $this->belongsToMany(Persona::class, 'famiglie_persone', 'famiglia_id', 'persona_id')
                ->withPivot("stato", 'posizione_famiglia', 'data_entrata', 'data_uscita')
                ->wherePivotIn('posizione_famiglia', ['FIGLIO NATO','FIGLIO ACCOLTO'])
                ->orderBy('data_nascita');
    }

    /**
    * Ritorna i figli attuali (sia nati che accolti) della famiglia.
    * @author Davide Neri
    **/
    public function figliAttuali()
    {
        return $this->figli()->wherePivot('stato', "=", '1');
    }

 
    /**
    * Rimuove tutti i componenti della famiglia da un gruppo familiare
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
            array('gruppoattuale' => $idGruppo, 'famigliaId'=> $this->id)
        );
    }


    /**
    * Assegna un nuovo gruppo familiare alla famiglia.
    * @author Davide Neri
    **/
    public function assegnaFamigliaANuovoGruppoFamiliare($gruppo_attuale_id, $dataUscitaGruppoFamiliareAttuale, $gruppo_nuovo_id, $data_entrata=null)
    {
        $famiglia_id = $this->id;
        return DB::transaction(function () use (&$gruppo_attuale_id, $dataUscitaGruppoFamiliareAttuale, &$famiglia_id, &$gruppo_nuovo_id, &$data_entrata) {
     
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
                array('gruppoattuale' => $gruppo_attuale_id, 'famigliaId'=> $famiglia_id , 'data_uscita'=>$dataUscitaGruppoFamiliareAttuale)
            );
    
            // Aggiungi a tutti i componenti della famiglia nel nuovo gruppo
            DB::connection('db_nomadelfia')->update(
                DB::raw("INSERT INTO gruppi_persone (persona_id, gruppo_famigliare_id, stato, data_entrata_gruppo)
              SELECT persone.id, :gruppo_nuovo_id, '1', :data_entrata
              FROM famiglie_persone
              INNER JOIN persone ON persone.id = famiglie_persone.persona_id
              WHERE famiglie_persone.famiglia_id = :famigliaId   AND famiglie_persone.stato = '1' "),
                array( 'famigliaId'=> $famiglia_id, 'gruppo_nuovo_id' => $gruppo_nuovo_id, 'data_entrata'=> $data_entrata)# , 'data_uscita'=>$dataUscitaGruppoFamiliareAttuale)
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
        $result->push((object)["descrizione" => "Famiglie non valide", "results" => $famiglie]);

    
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

        $result->push((object)["descrizione" => "Famiglie senza componenti o con nessun componente attivo", "results"=> $famiglieSenzaComponenti]);

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
        $result->push((object)["descrizione" => "Famiglie senza un CAPO FAMIGLIA", "results"=> $famiglieSenzaCapo]);

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
        $result->push((object)["descrizione" => "Famiglie assegnate in più di un grupo familiare", "results"=> $famiglieConPiuGruppi]);

        return $result;
    }

    /*
    *  Ritorna le persone Interne che non hanno una famiglia attiva.
    *
    */
    public static function personeSenzaFamiglia()
    {
        $personeSenzaFam = DB::connection('db_nomadelfia')->select(
            DB::raw("
        SELECT persone.id, persone.nominativo
        FROM persone
        INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
        WHERE persone.id NOT IN (
          SELECT famiglie_persone.persona_id
          FROM famiglie_persone
        )  AND persone_categorie.categoria_id = 1 and persone.stato = '1'
        ")
        );
        return $personeSenzaFam;
    }
}
