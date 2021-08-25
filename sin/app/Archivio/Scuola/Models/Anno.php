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
//    protected $fillable = ['responsabile_id', 'scolastico'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderby('data_inizio');
        });
    }

    public function responsabile()
    {
        return $this->belongsTo(Persona::class, 'responsabile_id','id');
    }

    public function aggiungiResponsabile(Persona $persona)
    {
        return $this->responsabile()->associate($persona);
    }

    public static function createAnno(int $year, $datainizo=null): Anno
    {
        $succ = $year + 1;
        $as = "{$year}/{$succ}";

        if ($datainizo === null){
            $d = Carbon::now();
        }else{
            $d = Carbon::parse($datainizo);
        }
        return self::create(['scolastico' => $as, 'data_inizio' =>$d]);
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
        return $this->classi()->create(["anno_id" => 3, 'tipo_id' => $tipo->id]);
    }

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

}
