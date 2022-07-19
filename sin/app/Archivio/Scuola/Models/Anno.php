<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Persona;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Anno extends Model
{

    public $timestamps = true;

    protected $connection = 'db_scuola';
    protected $table = 'anno';
    protected $primaryKey = "id";
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderby('data_inizio');
        });
    }

    public function responsabile()
    {
        return $this->belongsTo(Persona::class, 'responsabile_id', 'id');
    }

    public function aggiungiResponsabile(Persona $persona)
    {
        return $this->responsabile()->associate($persona);
    }

    public static function createAnno(int $year, $datainizo = null): Anno
    {
        $succ = $year + 1;
        $as = "{$year}/{$succ}";

        if ($datainizo === null) {
            $d = Carbon::now();
        } else {
            $d = Carbon::parse($datainizo);
        }
        return self::create(['scolastico' => $as, 'data_inizio' => $d]);
    }

    public static function getLastAnno(): Anno
    {
        $a = self::all();
        if ($a->count() > 0) {
            return $a->first();
        }
        throw new Exception("Non ci sono anni scolastici attivi");
    }

    public function classi()
    {
        return $this->hasMany(Classe::class, 'anno_id', 'id');
    }

    public function prescuola(){
        $p = ClasseTipo::Prescuola();
        return $this->classi()->where("tipo_id", "=", $p->id)->first();
    }

    public function elementari(){
        $p = ClasseTipo::Elementari()->get();
        return $this->classi()->whereIn("tipo_id", $p->pluck("id"))->get();
    }

    public function medie(){
        $p = ClasseTipo::Medie()->get();
        return $this->classi()->whereIn("tipo_id", $p->pluck("id"))->get();
    }

    public function superiori(){
        $p = ClasseTipo::Superiori()->get();
        return $this->classi()->whereIn("tipo_id", $p->pluck("id"))->get();
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

    public function aggiungiClasse(ClasseTipo $tipo): Classe
    {
        return $this->classi()->create(["anno_id" => $this->id, 'tipo_id' => $tipo->id]);
    }

//    @deprecated use the Studente::InAnnoScolastico()
    public function alunni()
    {
        $res = DB::connection('db_scuola')->select(
            DB::raw("SELECT p.*
                FROM alunni_classi as ac
                INNER JOIN classi ON classi.id = ac.classe_id
                INNER JOIN anno ON anno.id = classi.anno_id
                INNER JOIN db_nomadelfia.persone as p ON p.id = ac.persona_id
                WHERE ac.data_fine IS NULL AND anno.id = :aid
                ORDER BY p.data_nascita"),
            array('aid' => $this->id)
        );
        return $res;
    }


    public function coordinatoriPrescuola()
    {
        return $this->coordinatoriPerClassi("prescuola");
    }

    public function coordinatoriElementari()
    {
        return $this->coordinatoriPerClassi("elementari");
    }

    public function coordinatoriMedie()
    {
        return $this->coordinatoriPerClassi("medie");
    }

    public function coordinatorSuperiori()
    {
        return $this->coordinatoriPerClassi("superiori");
    }

    public function coordinatoriPerClassi(string $ciclo)
    {
        $res = DB::connection('db_scuola')->select(
            DB::raw("SELECT tipo.nome as classe, p.id as persona_id,  p.nominativo
                        FROM `coordinatori_classi`
                        INNER JOIN classi On classi.id = coordinatori_classi.classe_id
                        INNER JOIN tipo ON classi.tipo_id = tipo.id
                        INNER JOIN db_nomadelfia.persone as p ON p.id = coordinatori_classi.coordinatore_id
                        where coordinatori_classi.data_fine IS NULL AND classi.anno_id = :aid and tipo.ciclo = :ciclo
                        order by tipo.ord;"),
            array('aid' => $this->id, 'ciclo'=>$ciclo)
        );
        $cc = collect($res)->groupBy("classe");
        return $cc;
    }


}
