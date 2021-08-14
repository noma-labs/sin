<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\PopolazioneNomadelfia;
use Carbon;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{

    public $timestamps = true;

    protected $connection = 'db_scuola';
    protected $table = 'classi';
    protected $primaryKey = "id";
    protected $guarded = [];


    public function alunni()
    {
        return $this->belongsToMany(Persona::class, 'db_scuola.alunni_classi', 'classe_id',
            'persona_id')->withPivot('data_inizio');
    }

    public function anno()
    {
        return $this->belongsTo(Anno::class, "anno_id", 'id');
    }

    public function tipo()
    {
        return $this->belongsTo(ClasseTipo::class, "tipo_id", 'id');
    }

    public function aggiungiAlunno($alunno, Carbon\Carbon $data_inizio)
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

    public function alunniPossibili()
    {

        $all = PopolazioneNomadelfia::figliDaEta(3,21, "data_nascita");
        $currrent =  $this->alunni()->get();
        $multiplied = $currrent->map(function ($item, $key) {
            return $item->id;
        });
//        dd($multiplied);
        return $all->whereNotIn('id', $multiplied);
    }

}
