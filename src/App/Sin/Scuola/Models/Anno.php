<?php

namespace App\Scuola\Models;

use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @property string $data_inizio
 * @property string $scolastico
 */
class Anno extends Model
{
    public $timestamps = true;

    protected $connection = 'db_scuola';

    protected $table = 'anno';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderby('data_inizio');
        });
    }

    public function responsabile(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'responsabile_id', 'id');
    }

    public function aggiungiResponsabile(Persona $persona)
    {
        return $this->responsabile()->associate($persona);
    }

    public function nextAnnoScolasticoString()
    {
        $as = Str::of($this->scolastico)->explode('/');

        return $this->buildAsString((int) $as[1]);
    }

    public function annoSolareInizio()
    {
        $as = Str::of($this->scolastico)->explode('/');

        return $as[0];
    }

    public static function buildAsString(int $from_year): string
    {
        $succ = $from_year + 1;

        return "{$from_year}/{$succ}";
    }

    public static function createAnno(int $year, $datainizo = null, $with_classi = false): Anno
    {
        $as = self::buildAsString($year);

        if ($datainizo === null) {
            $d = Carbon::now();
        } else {
            $d = Carbon::parse($datainizo);
        }
        try {
            \DB::beginTransaction();
            $a = self::create(['scolastico' => $as, 'data_inizio' => $d]);
            if ($with_classi) {
                $t = ClasseTipo::all();
                foreach ($t as $tipo) {
                    if (! $tipo->isSuperiori()) {
                        $a->aggiungiClasse($tipo);
                    }
                }
            }
            \DB::commit();

            return $a;
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public static function cloneAnnoScolastico(Anno $copy_from_as, $data_inizio)
    {
        $nextas = $copy_from_as->nextAnnoScolasticoString();
        $a = self::create(['scolastico' => $nextas, 'data_inizio' => $data_inizio]);
        $classi_from = $copy_from_as->classi()->get();
        foreach ($classi_from as $classe) {
            $next = $classe->nextClasseTipo();
            if ($next) {
                $new_classe = $a->findOrCreateClasseByTipo($next);
                $new_classe->importStudentsFromOtherClasse($classe, $data_inizio);
            }
        }

        return $a;
    }

    public static function getLastAnno(): Anno
    {
        $a = self::orderBy('scolastico', 'DESC')->limit(1)->get();
        if ($a->count() > 0) {
            return $a->first();
        }
        throw new Exception('Non ci sono anni scolastici attivi');
    }

    public function classi()
    {
        return $this->hasMany(Classe::class, 'anno_id', 'id');
    }

    public function findOrCreateClasseByTipo(ClasseTipo $t): Classe
    {
        $c = $this->classi()->where('tipo_id', '=', $t->id)->first();
        if (! $c) {
            $c = $this->aggiungiClasse($t);
        }

        return $c;
    }

    public function prescuola()
    {
        $p = ClasseTipo::Prescuola();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function prescuola3Anni()
    {
        $p = ClasseTipo::Anni3Prescuola();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function prescuola4Anni()
    {
        $p = ClasseTipo::Anni4Prescuola();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function prescuola5Anni()
    {
        $p = ClasseTipo::Anni5Prescuola();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function elementari()
    {
        $p = ClasseTipo::Elementari()->get();

        return $this->classi()->whereIn('tipo_id', $p->pluck('id'))->get();
    }

    public function primaElementare()
    {
        $p = ClasseTipo::PrimaElem();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function secondaElementare()
    {
        $p = ClasseTipo::SecondaElem();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function terzaElementare()
    {
        $p = ClasseTipo::TerzaElem();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function quartaElementare()
    {
        $p = ClasseTipo::QuartaElem();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function quintaElementare()
    {
        $p = ClasseTipo::QuintaElem();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function medie()
    {
        $p = ClasseTipo::Medie()->get();

        return $this->classi()->whereIn('tipo_id', $p->pluck('id'))->get();
    }

    public function primaMedia()
    {
        $p = ClasseTipo::PrimaMed();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function secondaMedia()
    {
        $p = ClasseTipo::SecondaMed();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function terzaMedia()
    {
        $p = ClasseTipo::TerzaMed();

        return $this->classi()->where('tipo_id', '=', $p->id)->first();
    }

    public function superiori()
    {
        $p = ClasseTipo::Superiori()->get();

        return $this->classi()->with('tipo')->whereIn('tipo_id', $p->pluck('id'))->get()->sortBy('tipo.ord');
    }

    public function classiTipoPossibili()
    {
        $current = $this->classi()->get();
        $ids = $current->map(function ($item) {
            return $item->tipo->id;
        });
        $all = ClasseTipo::all();

        return $all->whereNotIn('id', $ids);
    }

    public function classiTipoAttuali()
    {
        $current = $this->classi()->with('tipo')->get();
        $ids = $current->map(function ($item) {
            return $item->tipo->id;
        });

        return ClasseTipo::whereIn('id', $ids);
    }

    public function aggiungiClasse(ClasseTipo $tipo): Classe
    {
        return $this->classi()->create(['anno_id' => $this->id, 'tipo_id' => $tipo->id]);
    }

    //    @deprecated use the Studente::InAnnoScolastico()
    public function alunni()
    {
        $expression = DB::raw('SELECT p.*
                FROM alunni_classi as ac
                INNER JOIN classi ON classi.id = ac.classe_id
                INNER JOIN anno ON anno.id = classi.anno_id
                INNER JOIN db_nomadelfia.persone as p ON p.id = ac.persona_id
                WHERE ac.data_fine IS NULL AND anno.id = :aid
                ORDER BY p.data_nascita');

        return DB::connection('db_scuola')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['aid' => $this->id]
        );
    }

    public function coordinatoriPrescuola()
    {
        return $this->coordinatoriPerClassi('prescuola');
    }

    public function coordinatoriElementari()
    {
        return $this->coordinatoriPerClassi('elementari');
    }

    public function coordinatoriMedie()
    {
        return $this->coordinatoriPerClassi('medie');
    }

    public function coordinatorSuperiori()
    {
        return $this->coordinatoriPerClassi('superiori');
    }

    public function coordinatoriPerClassi(string $ciclo)
    {
        $expression = DB::raw('SELECT tipo.nome as classe, p.id as persona_id,  p.nominativo
                        FROM `coordinatori_classi`
                        INNER JOIN classi On classi.id = coordinatori_classi.classe_id
                        INNER JOIN tipo ON classi.tipo_id = tipo.id
                        INNER JOIN db_nomadelfia.persone as p ON p.id = coordinatori_classi.coordinatore_id
                        where coordinatori_classi.data_fine IS NULL AND classi.anno_id = :aid and tipo.ciclo = :ciclo
                        order by tipo.ord;');
        $res = DB::connection('db_scuola')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['aid' => $this->id, 'ciclo' => $ciclo]
        );

        return collect($res)->groupBy('classe');
    }
}
