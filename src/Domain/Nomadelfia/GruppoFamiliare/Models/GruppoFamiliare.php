<?php

namespace Domain\Nomadelfia\GruppoFamiliare\Models;

use App\Nomadelfia\Exceptions\CouldNotAssignCapogruppo;
use App\Nomadelfia\Exceptions\GruppoHaMultipleCapogruppi;
use App\Nomadelfia\Exceptions\GruppoHasMultipleCapogruppi;
use Database\Factories\GruppoFamiliareFactory;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GruppoFamiliare extends Model
{
    use HasFactory;

    protected $connection = 'db_nomadelfia';

    protected $table = 'gruppi_familiari';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];

    protected static function newFactory()
    {
        return GruppoFamiliareFactory::new();
    }

    public function capogruppi()
    {
        return $this->belongsToMany(Persona::class, 'gruppi_familiari_capogruppi', 'gruppo_familiare_id', 'persona_id');
    }

    public function capogruppoAttuale()
    {
        $cp = $this->capogruppi()->wherePivot('stato', 1)->get();
        if ($cp->count() == 1) {
            return $cp[0];
        } elseif ($cp->count() == 0) {
            return null;
        } else {
            throw GruppoHaMultipleCapogruppi::named($this);
        }
    }

    /**
     *  Ritorna le persone che possono fare il capogruppo (maschi, nomadelfi effettivi??)
     */
    public function capogruppiPossibili()
    {
        $effetivo = Posizione::perNome('effettivo');
        $attuale = $this->capogruppoAttuale();
        $res = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT * 
                FROM persone
                INNER JOIN gruppi_persone ON gruppi_persone.persona_id = persone.id
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                WHERE gruppi_persone.stato = '1' AND persone_posizioni.stato = '1'  AND persone_posizioni.posizione_id = :effe
                    AND gruppi_persone.gruppo_famigliare_id = :gr AND persone.sesso = 'M' AND gruppi_persone.persona_id != :capoatt
                ORDER BY persone.data_nascita ASC"
            ),
            [
                'gr' => $this->id,
                'effe' => $effetivo->id,
                'capoatt' => $attuale ? $attuale->id : '',
            ]
        );

        return $res;
    }

    /**
     *  Assegna un nuovo capogruppo
     */
    public function assegnaCapogruppo($persona, $data_inizio)
    {
        // TODO: controllare che la persona sia un mascho e nomadeflo effettivo
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            if (!$persona->isEffettivo()) {
                throw CouldNotAssignCapogruppo::isNotEffetivo($persona);
            }
            if (!$persona->isMaschio()) {
                throw CouldNotAssignCapogruppo::isNotAMan($persona);
            }
            DB::connection('db_nomadelfia')->beginTransaction();
            try {
                $attuale = $this->capogruppoAttuale();
                if ($attuale) {
                    $this->capogruppi()->updateExistingPivot($attuale->id, [
                        'stato' => '0',
                        'data_fine_incarico' => $data_inizio,
                    ]);
                }
                $this->capogruppi()->attach($persona->id, ['stato' => '1', 'data_inizio_incarico' => $data_inizio]);
                DB::connection('db_nomadelfia')->commit();
            } catch (\Exception $e) {
                DB::connection('db_nomadelfia')->rollback();
                throw $e;
            }
        } else {
            throw new Exception('Bad Argument. Personae must be an id or a model.');
        }

    }

    public function persone()
    {
        return $this->belongsToMany(Persona::class, 'gruppi_persone', 'gruppo_famigliare_id', 'persona_id')
            ->withPivot('stato')
            ->orderBy('data_nascita', 'ASC');
    }

    public function personeAttuale()
    {
        return $this->persone()->wherePivot('stato', '1');
    }

    /*
    * Ritorna il numero di componenti per un singolo gruppo familiare
    */
    public function componenti()
    {
        $gruppi = DB::connection('db_nomadelfia')->select(
            DB::raw("Select *
                from persone
                where persone.id IN (
                    SELECT gruppi_persone.persona_id
                    from gruppi_persone
                    where gruppi_persone.stato = '1'
                      AND gruppi_persone.gruppo_famigliare_id = 9
                )
                order by data_nascita")
        );

        return $gruppi;
    }

    /*
    * Ritorna il numero di componenti per ogni gruppi familiare
   */
    public static function countComponenti()
    {
        $gruppi = DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT gruppi_persone.gruppo_famigliare_id as id, max(gruppi_familiari.nome) as nome, count(*) as count
                            from gruppi_persone
                            left join gruppi_familiari on gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
                            where gruppi_persone.stato = '1'
                            group by gruppi_persone.gruppo_famigliare_id
                            order by gruppi_familiari.nome"
            )
        );

        return $gruppi;
    }

    /*
    * Ricostruisce le famiglie del gruppo familiare partendo dalle persone presenti.
    *
    */
    public function Famiglie()
    {
        $famiglie = DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita 
      FROM gruppi_persone 
      LEFT JOIN famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id 
      LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id 
      INNER JOIN persone ON gruppi_persone.persona_id = persone.id 
      WHERE gruppi_persone.gruppo_famigliare_id = :gruppo 
          AND gruppi_persone.stato = '1' 
          AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL)
          AND (famiglie_persone.posizione_famiglia != 'SINGLE' OR famiglie_persone.stato IS NULL)
      ORDER BY  famiglie.nome_famiglia, famiglie_persone.posizione_famiglia ASC, persone.data_nascita ASC"), ['gruppo' => $this->id]);

        $famiglie = collect($famiglie)->groupBy('famiglia_id');

        return $famiglie;
    }

    /*
    * Ritorna famiglie SINGLE del gruppo familiare partendo dalle persone presenti.
    * Il controllo nella query (famiglie_persone.stato IS NULL) viene usato per selezionare anche le persone senza una famiglia.
    */
    public function Single()
    {
        $single = DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita 
              FROM gruppi_persone 
              LEFT JOIN famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id 
              INNER JOIN persone ON gruppi_persone.persona_id = persone.id 
              LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id 
              WHERE gruppi_persone.gruppo_famigliare_id = :gruppo
                  AND gruppi_persone.stato = '1' 
                  AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL) 
                  AND (famiglie_persone.posizione_famiglia = 'SINGLE' OR famiglie_persone.stato IS NULL)
              ORDER BY persone.sesso DESC, persone.nominativo, persone.data_nascita  ASC"),
            ['gruppo' => $this->id]
        );

        return $single;
    }

    public function isCentroDiSpirito(): bool
    {
        return Str::lower($this->nome) === Str::lower('GIOVANNI PAOLO II'); // Giovanni Paolo II
    }
}
