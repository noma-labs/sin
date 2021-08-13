<?php

namespace App\Scuola\Models;

use App\Scuola\Exceptions\CouldNotAssignAlunno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Classe;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classe extends Model
{

    public $timestamps = true;

    protected $connection = 'db_scuola';
    protected $table = 'classi';
    protected $primaryKey = "id";
    protected $guarded = [];

    public static function perAnno(string $anno){
        return self::where("anno", $anno)->get();
    }

    public function alunni()
    {
        return $this->belongsToMany(Persona::class, 'db_scuola.alunni_classi', 'classe_id', 'persona_id')->withPivot('data_inizio');
    }

    public function tipo()
    {
        return $this->belongsTo(ClasseTipo::class,"tipo_id",'id');
    }

    public function aggiungiAlunno(Persona $alunno, Carbon\Carbon $data_inizio)
    {
        if (is_string($alunno)) {
            $alunno = Persona::findOrFail($alunno);
        }
        if ($alunno instanceof Persona) {
            $this->alunni()->attach($alunno->id, [
                'data_inizio' => $data_inizio,
            ]);
        }
    }


}
