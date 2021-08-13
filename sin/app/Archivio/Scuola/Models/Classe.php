<?php

namespace App\Nomadelfia\Models;

use App\Scuola\Exceptions\CouldNotAssignAlunno;
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


    public function alunni()
    {
        return $this->belongsToMany(Persona::class, 'db_scuola.alunni_classi', 'classe_id', 'persona_id')->withPivot('data_inizio');
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

    public static function aggiungiClasse(string $annoScolastico, ClasseTipo $tipo): Classe
    {
        if (!Str::contains($annoScolastico, '/')){
            throw CouldNotAssignAlunno::isNotValidAnno($annoScolastico);
        }

        //TODO: check that anno scolastico is of the form "AAAA/AAAA"
        $c = self::create([
            'tipo_id' => $tipo->id,
            'anno' => $annoScolastico
        ]);
        return $c;
    }

}
