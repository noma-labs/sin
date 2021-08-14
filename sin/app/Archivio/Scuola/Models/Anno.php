<?php

namespace App\Scuola\Models;

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
            $builder->orderby('anno');
        });
    }

    public static function createAnno(int $year)
    {
        $succ = $year + 1;
        $as = "{$year}/{$succ}";
        return self::create(["anno" => $year, 'scolastico' => $as]);
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
