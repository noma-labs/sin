<?php

namespace App\Scuola\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Anno extends Model
{

    public $timestamps = true;

    protected $connection = 'db_scuola';
    protected $table = 'anno';
    protected $primaryKey = "id";
    protected $guarded = [];

    public static function createAnno(int $year)
    {
        $succ = $year + 1;
        $as = "{$year}/{$succ}";
        return self::create([ "anno" => $year, 'scolastico' => $as]);
    }

    public function classi()
    {
        return $this->hasMany(Classe::class, 'anno_id', 'id');
    }

    public function aggiungiClasse(ClasseTipo $tipo): Classe
    {
        return $this->classi()->create(["anno_id" => 3, 'tipo_id' => $tipo->id]);
    }

}
