<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\GruppoFamiliare\Models;

use App\Nomadelfia\Exceptions\CouldNotAssignCapogruppo;
use App\Nomadelfia\Exceptions\GruppoHaMultipleCapogruppi;
use Carbon\Carbon;
use Database\Factories\GruppoFamiliareFactory;
use Domain\Nomadelfia\GruppoFamiliare\QueryBuilders\GruppoFamiliareQueryBuilder;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @property string $nome
 * @property int $id
 */
final class GruppoFamiliare extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $connection = 'db_nomadelfia';

    protected $table = 'gruppi_familiari';

    protected $primaryKey = 'id';

    protected $guarded = [];

    /*
    * Ritorna il numero di componenti per ogni gruppi familiare
   */
    public static function countComponenti()
    {
        $expression = DB::raw("SELECT gruppi_persone.gruppo_famigliare_id as id, max(gruppi_familiari.nome) as nome, count(*) as count
                            from gruppi_persone
                            left join gruppi_familiari on gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
                            where gruppi_persone.stato = '1'
                            group by gruppi_persone.gruppo_famigliare_id
                            order by gruppi_familiari.nome"
        );

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
        );
    }

    public function newEloquentBuilder($query): GruppoFamiliareQueryBuilder
    {
        return new GruppoFamiliareQueryBuilder($query);
    }

    public function capogruppi()
    {
        return $this->belongsToMany(Persona::class, 'gruppi_familiari_capogruppi', 'gruppo_familiare_id', 'persona_id');
    }

    public function capogruppoAttuale()
    {
        $cp = $this->capogruppi()->wherePivot('stato', 1)->get();
        if ($cp->count() === 1) {
            return $cp[0];
        }
        if ($cp->count() === 0) {
            return null;
        }
        throw GruppoHaMultipleCapogruppi::named($this);
    }

    /**
     *  Ritorna le persone che possono fare il capogruppo (maschi, nomadelfi effettivi??)
     */
    public function capogruppiPossibili()
    {
        $effetivo = Posizione::perNome('effettivo');
        $attuale = $this->capogruppoAttuale();
        $expression = DB::raw(
            "SELECT *
                FROM persone
                INNER JOIN gruppi_persone ON gruppi_persone.persona_id = persone.id
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                WHERE gruppi_persone.stato = '1' AND persone_posizioni.stato = '1'  AND persone_posizioni.posizione_id = :effe
                    AND gruppi_persone.gruppo_famigliare_id = :gr AND persone.sesso = 'M' AND gruppi_persone.persona_id != :capoatt
                ORDER BY persone.data_nascita ASC"
        );

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            [
                'gr' => $this->id,
                'effe' => $effetivo->id,
                'capoatt' => $attuale ? $attuale->id : '',
            ]
        );
    }

    public function assegnaCapogruppo(Persona|int $persona, Carbon $data_inizio): void
    {
        // TODO: controllare che la persona sia un mascho e nomadelfo effettivo
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            if (! $persona->isEffettivo()) {
                throw CouldNotAssignCapogruppo::isNotEffetivo($persona);
            }
            if (! $persona->isMaschio()) {
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
            } catch (Exception $e) {
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
        $expression = DB::raw("Select *
                from persone
                where persone.id IN (
                    SELECT gruppi_persone.persona_id
                    from gruppi_persone
                    where gruppi_persone.stato = '1'
                      AND gruppi_persone.gruppo_famigliare_id = 9
                )
                order by data_nascita");

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
        );
    }

    /*
    * Ricostruisce le famiglie del gruppo familiare partendo dalle persone presenti.
    *
    */
    //    public function Famiglie()
    //    {
    //        $famiglie = DB::connection('db_nomadelfia')->select(
    //            DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita
    //      FROM gruppi_persone
    //      LEFT JOIN famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id
    //      LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id
    //      INNER JOIN persone ON gruppi_persone.persona_id = persone.id
    //      WHERE gruppi_persone.gruppo_famigliare_id = :gruppo
    //          AND gruppi_persone.stato = '1'
    //          AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL)
    //          AND (famiglie_persone.posizione_famiglia != 'SINGLE' OR famiglie_persone.stato IS NULL)
    //      ORDER BY  famiglie.nome_famiglia, persone.data_nascita ASC"), ['gruppo' => $this->id]);
    //
    //        $famiglie = collect($famiglie)->groupBy('famiglia_id');
    //
    //        return $famiglie;
    //    }

    public function isCentroDiSpirito(): bool
    {
        return Str::lower($this->nome) === Str::lower('GIOVANNI PAOLO II'); // Giovanni Paolo II
    }

    protected static function newFactory()
    {
        return GruppoFamiliareFactory::new();
    }
}
