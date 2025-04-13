<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Models;

use App\Nomadelfia\Exceptions\CouldNotAssignCapoFamiglia;
use App\Nomadelfia\Exceptions\CouldNotAssignMoglie;
use App\Nomadelfia\Exceptions\FamigliaHasMultipleGroup;
use App\Nomadelfia\Exceptions\FamigliaHasNoGroup;
use App\Nomadelfia\Famiglia\QueryBuilders\FamigliaQueryBuilder;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Traits\Enums;
use Carbon\Carbon;
use Database\Factories\FamigliaFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @property int $id.
 * @property string $nome_famiglia.
 * @property string $data_creazione.
 */
final class Famiglia extends Model
{
    use Enums;
    use HasFactory;

    protected $connection = 'db_nomadelfia';

    protected $table = 'famiglie';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $enumPosizione = [
        // TODO: sostiture  CAPO FAMIGLIA con 'marito' e aggiungere una nuova colonna `capo_famiglia` per identificare chi è il capo famiglia
        // il nome della famiglia è uguale al nome del capo famiglia.
        'CAPO FAMIGLIA',
        'MOGLIE',
        'FIGLIO NATO',
        'FIGLIO ACCOLTO',
    ];

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

    public static function figliEnums()
    {
        return collect(self::getEnum('Posizione'))->filter(fn ($value, $key) => Str::startsWith($value, 'FIGLIO'));
    }

    /**
     * Returns the families with a CAPO FAMIGLIA (men or women)
     */
    public static function conCapofamiglia()
    {
        $expression = DB::raw("SELECT famiglie.*
              FROM `famiglie`
              INNER JOIN famiglie_persone on famiglie_persone.famiglia_id = famiglie.id
              WHERE famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
              ORDER BY famiglie.nome_famiglia");

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
        );
    }

    /**
     * Ritorna tutti capi famiglia delle famiglie
     *
     * @author Davide Neri
     **/
    public static function OnlyCapofamiglia()
    {
        /** @phpstan-ignore-next-line */
        return self::FamigliePerPosizioni('CAPO FAMIGLIA');
    }

    public static function famiglieNumerose(int $min_componenti = 5)
    {
        $expression = DB::raw("WITH famiglie_numerose AS (
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
                    order by famiglie_numerose.componenti DESC;");

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['minc' => $min_componenti]
        );
    }

    /*
    *  Ritorna le famiglie che hanno un errore nei loro componenti
    *   2) ci sono più CAPO FAMIGLIA attivi nella famiglia
    *   3) ci sono più di una MOGLIE nella famiglia
    * Ritonra le famiglie senza componenti
    */
    public static function famigliaConErrore()
    {
        $result = collect();
        $expression = DB::raw(
            "SELECT famiglie.id, famiglie.nome_famiglia
              from (
                  SELECT famiglie_persone.famiglia_id, famiglie_persone.posizione_famiglia, count(*) as count
                  FROM famiglie_persone
                  WHERE famiglie_persone.stato = '1'
                  GROUP BY famiglie_persone.famiglia_id, famiglie_persone.posizione_famiglia
              ) AS g
              INNER JOIN famiglie ON famiglie.id = g.famiglia_id
              WHERE (g.posizione_famiglia = 'CAPO FAMIGLIA' AND g.count>1) OR (g.posizione_famiglia = 'MOGLIE' AND g.count>1)"
        );
        $famiglie = DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        );
        $result->push((object) ['descrizione' => 'Famiglie non valide', 'results' => $famiglie]);

        $expression = DB::raw(
            "SELECT *
              FROM famiglie
              WHERE famiglie.id NOT IN (
              SELECT famiglie_persone.famiglia_id
                FROm famiglie_persone
                WHERE famiglie_persone.stato = '1'
                GROUP BY famiglie_persone.famiglia_id
              )"
        );
        $famiglieSenzaComponenti = DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        );

        $result->push((object) [
            'descrizione' => 'Famiglie senza componenti o con nessun componente attivo',
            'results' => $famiglieSenzaComponenti,
        ]);
        $expression = DB::raw("
      SELECT famiglie.*
      FROM  famiglie
      WHERE famiglie.id NOT IN (
           SELECT famiglie_persone.famiglia_id
           FROM famiglie_persone
           WHERE famiglie_persone.stato = '1' AND (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA')
      )
        ");

        $famiglieSenzaCapo = DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        );
        $result->push((object) ['descrizione' => 'Famiglie senza un CAPO FAMIGLIA', 'results' => $famiglieSenzaCapo]);

        $expression = DB::raw("SELECT *
              from famiglie
              WHERE famiglie.id IN (
                SELECT famiglie_persone.famiglia_id
                FROM famiglie_persone
                INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
                INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
                WHERE famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'  and gruppi_persone.stato = '1' and  famiglie_persone.stato = '1'
                GROUP BY famiglie_persone.famiglia_id
                HAVING count(*) > 1
              )");
        $famiglieConPiuGruppi = DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        );
        $result->push((object) [
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
        $expression = DB::raw('
        SELECT persone.id, persone.nominativo
        FROM persone
        INNER JOIN popolazione ON popolazione.persona_id = persone.id
        WHERE persone.id NOT IN (
          SELECT famiglie_persone.persona_id
          FROM famiglie_persone
        )  AND popolazione.data_uscita IS NULL');

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        );
    }

    public function newEloquentBuilder($query): FamigliaQueryBuilder
    {
        return new FamigliaQueryBuilder($query);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('nome_famiglia', 'asc')->get();
    }

    public function scopeFamigliePerPosizioni($query, $posizione, $stato = '1')
    {
        return $query->select('famiglie.*', 'persone.sesso', 'famiglie_persone.posizione_famiglia',
            'famiglie_persone.stato')
            ->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'famiglie.id')
            ->join('persone', 'famiglie_persone.persona_id', '=', 'persone.id')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->whereNull('popolazione.data_uscita')
            ->where('posizione_famiglia', $posizione)
            ->where('famiglie_persone.stato', $stato)
            ->orderBy('famiglie.nome_famiglia');
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
     * Uscita di una famiglia da Nomadelfia.
     * Esegure la funzione di uscita su tutti componenti ATTIVI della famiglia.
     *
     * @author Davide Neri
     **/
    public function uscita(Carbon $data_uscita): void
    {
        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $this->componentiAttuali()->get()->each(function ($componente) use ($data_uscita): void {
                $act = app(UscitaPersonaAction::class);
                $act->execute($componente, $data_uscita);
            });
            DB::connection('db_nomadelfia')->commit();
        } catch (Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }

    /**
     * Ritorna il gruppo familiare attuale in cui vive il CAPO FAMIGLIA
     * Si assume che tutta la famiglia vive nello stesso gruppo del CAPO FAMIGLIA
     *
     * @author Davide Neri
     **/
    public function gruppoFamiliareAttuale()
    {
        $expression = DB::raw("SELECT gruppi_familiari.*, gruppi_persone.data_entrata_gruppo
            FROM famiglie_persone
            INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
            INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
            WHERE famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
                and famiglie_persone.famiglia_id = :famiglia_id and gruppi_persone.stato = '1' and famiglie_persone.stato = '1'");
        $res = collect(DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['famiglia_id' => $this->id]
        ));
        if ($res->count() === 1) {
            return $res->first();
        }
        if ($res->count() === 0) {
            return null;
        }
        throw FamigliaHasMultipleGroup::named($this);
    }

    public function gruppoFamiliareAttualeOrFail()
    {
        $gruppo = $this->gruppoFamiliareAttuale();
        if ($gruppo === null) {
            throw FamigliaHasNoGroup::named($this->nome_famiglia);
        }

        return $gruppo;
    }

    /**
     * Ritorna i gruppi familiari storici in cui ha vissuto il CAPO FAMIGLIA della famiglia
     *
     * @author Davide Neri
     **/
    public function gruppiFamiliariStorico()
    {
        $expression = DB::raw("SELECT gruppi_familiari.*, gruppi_persone.data_entrata_gruppo, gruppi_persone.data_uscita_gruppo
      FROM famiglie_persone
      INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
      INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
      WHERE (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA')
       and famiglie_persone.famiglia_id = :famiglia_id and gruppi_persone.stato = '0'");

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['famiglia_id' => $this->id]
        );
    }

    public function mycomponenti()
    {
        $expresson = DB::raw('SELECT famiglie.id, famiglie_persone.*, persone.id, persone.nominativo, persone.data_nascita
                    FROM famiglie
                    INNER JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id
                    INNER JOIN persone ON persone.id = famiglie_persone.persona_id
                    WHERE famiglie.id = :famiglia
                    ORDER BY persone.data_nascita, famiglie_persone.posizione_famiglia');

        return DB::connection('db_nomadelfia')->select(
            $expresson->getValue(DB::connection()->getQueryGrammar()),
            ['famiglia' => $this->id]
        );
    }

    /**
     * Aggiunge il capo famiglia alla famiglia
     *
     * @author Davide Neri
     **/
    public function assegnaCapoFamiglia($persona, $note = null)
    {

        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $capo = $this->capofamiglia();
            if ($capo !== null) {
                throw CouldNotAssignCapoFamiglia::hasAlreadyCapoFamiglia($this, $capo);
            }
            if ($persona->isMaggiorenne() !== true) {
                throw CouldNotAssignCapoFamiglia::beacuseIsMinorenne($this, $persona);
            }

            return $this->assegnaComponente($persona, self::getCapoFamigliaEnum());
        }
        throw new InvalidArgumentException("Identiticativo `{$persona}` della persona non valido.");
    }

    /**
     * Aggiunge la moglie alla famiglia
     *
     * @author Davide Neri
     **/
    public function assegnaMoglie($persona)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            if ($this->moglie() !== null) {
                throw CouldNotAssignMoglie::hasAlreadyMoglie($this, $persona);
            }
            if ($persona->isMaggiorenne() === false) {
                throw CouldNotAssignMoglie::beacuseIsMinorenne($this, $persona);
            }
            if ($persona->isMaschio() === true) {
                throw CouldNotAssignMoglie::beacuseIsMan($this, $persona);
            }

            return $this->assegnaComponente($persona, self::getMoglieEnum());
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    public function assegnaSingle($persona, $data = null, $note = null)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $data = $data ?: Carbon::parse($persona->data_nascita)->addYears(18)->toDateString();

            /** @phpstan-ignore-next-line */
            return $this->assegnaComponente($persona, $this->getSingleEnum());
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
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->assegnaComponente($persona, self::getFiglioNatoEnum());
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    /**
     * Aggiunge un figlio accolto nella famiglia
     *
     * @author Davide Neri
     **/
    public function assegnaFiglioAccolto($persona, $note = null)
    {
        // TODO: check the la persona non ha già una famiglia associata
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->assegnaComponente($persona, self::getFiglioAccoltoEnum());
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    public function assegnaComponente($persona, string $posizione, string $stato = '1', $note = null)
    {
        if (! in_array($posizione, $this->enumPosizione)) {
            throw new InvalidArgumentException("La posizione `{$posizione}` è invalida");
        }

        return $this->componenti()->attach($persona->id,
            ['stato' => $stato, 'posizione_famiglia' => $posizione, 'note' => $note]);
    }

    /**
     * Fa uscire un figlio dal nucleo familiare.
     * Il figlio rimane nucleo familiare come "fuori nucleo familiare".
     *
     * @author Davide Neri
     **/
    public function uscitaDalNucleoFamiliare($persona, $note = null)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->componenti()->updateExistingPivot($persona->id,
                ['stato' => '0', 'note' => $note]);
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
            ->withPivot('stato', 'posizione_famiglia')
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
            ->withPivot('stato', 'posizione_famiglia')
            ->wherePivot('posizione_famiglia', 'CAPO FAMIGLIA')
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
            ->withPivot('stato', 'posizione_famiglia')
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
    public function rimuoviDaGruppoFamiliare($idGruppo): void
    {
        $expression = DB::raw("UPDATE gruppi_persone
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
                AND gruppi_persone.stato = '1' ");
        DB::connection('db_nomadelfia')->update(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['gruppoattuale' => $idGruppo, 'famigliaId' => $this->id]
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
    ) {
        $famiglia_id = $this->id;

        return DB::transaction(function () use (
            &$gruppo_attuale_id,
            $dataUscitaGruppoFamiliareAttuale,
            &$famiglia_id,
            &$gruppo_nuovo_id,
            &$data_entrata
        ): bool {

            // Disabilita tutti i componenti della famiglia dal vecchio gruppo (mette stato = 0)
            $expression = DB::raw("UPDATE gruppi_persone
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

                AND gruppi_persone.stato = '1' ");

            DB::connection('db_nomadelfia')->update(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                [
                    'gruppoattuale' => $gruppo_attuale_id,
                    'famigliaId' => $famiglia_id,
                    'data_uscita' => $dataUscitaGruppoFamiliareAttuale,
                ]
            );

            // Aggiungi a tutti i componenti della famiglia nel nuovo gruppo
            $expr = DB::raw("INSERT INTO gruppi_persone (persona_id, gruppo_famigliare_id, stato, data_entrata_gruppo)
              SELECT persone.id, :gruppo_nuovo_id, '1', :data_entrata
              FROM famiglie_persone
              INNER JOIN persone ON persone.id = famiglie_persone.persona_id
              WHERE famiglie_persone.famiglia_id = :famigliaId   AND famiglie_persone.stato = '1' ");
            DB::connection('db_nomadelfia')->update(
                $expr->getValue(DB::connection()->getQueryGrammar()),
                [
                    'famigliaId' => $famiglia_id,
                    'gruppo_nuovo_id' => $gruppo_nuovo_id,
                    'data_entrata' => $data_entrata,
                ]// , 'data_uscita'=>$dataUscitaGruppoFamiliareAttuale)
            );

            return true;
        });
    }

    protected static function newFactory()
    {
        return FamigliaFactory::new();
    }

    /**
     * Set the nome in uppercase when a new famiglia is insereted.
     */
    protected function nomeFamiglia(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: fn ($value): array => ['nome_famiglia' => ucwords(mb_strtolower((string) $value))]);
    }
}
