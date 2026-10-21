<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Models;

use App\Anagrafe\Exceptions\CouldNotAssignCapoFamiglia;
use App\Anagrafe\Exceptions\CouldNotAssignMoglie;
use App\Anagrafe\Famiglia\QueryBuilders\FamigliaQueryBuilder;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Concerns\FamigliaInNomadelfia;
use App\Traits\Enums;
use Database\Factories\FamigliaFactory;
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
    use FamigliaInNomadelfia;
    use HasFactory;

    protected $connection = 'db_nomadelfia';

    protected $table = 'famiglie';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $enumPosizione = [
        // TODO: replace CAPO FAMIGLIA with 'marito' and add a new column `capo_famiglia` to identify who is the head of the family
        // the family name is the same as the head of the family.
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

    public static function OnlyCapofamiglia()
    {
        /** @phpstan-ignore-next-line */
        return self::query()->famigliePerPosizioni('CAPO FAMIGLIA');
    }

    /*
    *  Returns families with errors in their members
    *   2) there are multiple active HEAD OF FAMILY in the family
    *   3) there is more than one WIFE in the family
    * Returns families without members
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
        $result->push((object) ['descrizione' => 'Invalid families', 'results' => $famiglie]);

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
            'descrizione' => 'Families without members or with no active member',
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
        $result->push((object) ['descrizione' => 'Families without a HEAD OF FAMILY', 'results' => $famiglieSenzaCapo]);

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
            'descrizione' => 'Families assigned to more than one family group',
            'results' => $famiglieConPiuGruppi,
        ]);

        return $result;
    }

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
     * @param  string  $stato  '1' in the nucleo, '0' outside the nucleo (e.g. the deceased husband of a vedova who heads the famiglia)
     */
    public function assegnaCapoFamiglia($persona, $note = null, string $stato = '1')
    {

        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $capo = $stato === '1' ? $this->capofamiglia() : null;
            if ($capo !== null) {
                throw CouldNotAssignCapoFamiglia::hasAlreadyCapoFamiglia($this, $capo);
            }
            if ($persona->isMaggiorenne() !== true) {
                throw CouldNotAssignCapoFamiglia::beacuseIsMinorenne($this, $persona);
            }

            return $this->assegnaComponente($persona, self::getCapoFamigliaEnum(), $stato, $note);
        }
        throw new InvalidArgumentException("Identiticativo `{$persona}` della persona non valido.");
    }

    /**
     * @param  string  $stato  '1' in the nucleo, '0' outside the nucleo (e.g. a deceased moglie)
     */
    public function assegnaMoglie($persona, string $stato = '1')
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            if ($stato === '1' && $this->moglie() !== null) {
                throw CouldNotAssignMoglie::hasAlreadyMoglie($this, $persona);
            }
            if ($persona->isMaggiorenne() === false) {
                throw CouldNotAssignMoglie::beacuseIsMinorenne($this, $persona);
            }
            if ($persona->isMaschio() === true) {
                throw CouldNotAssignMoglie::beacuseIsMan($this, $persona);
            }

            return $this->assegnaComponente($persona, self::getMoglieEnum(), $stato);
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    public function assegnaSingle($persona, $data = null, $note = null)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $data = $data ?: \Illuminate\Support\Facades\Date::parse($persona->data_nascita)->addYears(18)->toDateString();

            /** @phpstan-ignore-next-line */
            return $this->assegnaComponente($persona, $this->getSingleEnum());
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    public function assegnaFiglioNato($persona, $note = null, string $stato = '1')
    {
        // TODO: check the la persona non ha già una famiglia associata
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->assegnaComponente($persona, self::getFiglioNatoEnum(), $stato, $note);
        }
        throw new InvalidArgumentException('Bad person as argument. It must be the id or the model of a person.');
    }

    public function assegnaFiglioAccolto($persona, $note = null, string $stato = '1')
    {
        // TODO: check the la persona non ha già una famiglia associata
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            return $this->assegnaComponente($persona, self::getFiglioAccoltoEnum(), $stato, $note);
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
     * Returns the members who have been part of the family (father, mother, and children)
     *
     * @Deprecated
     * Use the mycomponenti()
     **/
    public function componenti()
    {
        return $this->belongsToMany(Persona::class, 'famiglie_persone', 'famiglia_id', 'persona_id')
            ->withPivot('stato', 'posizione_famiglia')
            ->orderby('nominativo');
    }

    public function componentiAttuali()
    {
        return $this->componenti()->where('famiglie_persone.stato', '1');
    }

    /**
     * The capofamiglia in the nucleo. A deceased husband stays in the famiglia as capofamiglia outside the nucleo,
     * next to the vedova who heads the famiglia.
     */
    public function capofamiglia()
    {
        return $this->componentiAttuali()
            ->wherePivot('posizione_famiglia', 'CAPO FAMIGLIA')
            ->first();
    }

    /**
     * The moglie in the nucleo.
     */
    public function moglie()
    {
        return $this->componentiAttuali()
            ->wherePivot('posizione_famiglia', 'MOGLIE')
            ->first();
    }

    public function figli()
    {
        return $this->belongsToMany(Persona::class, 'famiglie_persone', 'famiglia_id', 'persona_id')
            ->withPivot('stato', 'posizione_famiglia')
            ->wherePivotIn('posizione_famiglia', ['FIGLIO NATO', 'FIGLIO ACCOLTO'])
            ->orderBy('data_nascita');
    }

    public function figliAttuali()
    {
        return $this->figli()->wherePivot('stato', '=', '1');
    }

    protected static function newFactory()
    {
        return FamigliaFactory::new();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function ordered($query)
    {
        return $query->orderBy('nome_famiglia', 'asc')->get();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function famigliePerPosizioni($query, $posizione, $stato = '1')
    {
        return $query->select('famiglie.*', 'persone.id as persona_id', 'persone.sesso', 'famiglie_persone.posizione_famiglia',
            'famiglie_persone.stato')
            ->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'famiglie.id')
            ->join('persone', 'famiglie_persone.persona_id', '=', 'persone.id')
            ->whereNull('persone.data_decesso')
            ->whereNull('persone.deleted_at')
            ->where('posizione_famiglia', $posizione)
            ->where('famiglie_persone.stato', $stato)
            ->orderBy('famiglie.nome_famiglia');
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function maschio($query)
    {
        return $query->where('sesso', 'M');
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function femmina($query)
    {
        return $query->where('sesso', 'F');
    }

    protected function nomeFamiglia(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: fn ($value): array => ['nome_famiglia' => ucwords(mb_strtolower((string) $value))]);
    }
}
