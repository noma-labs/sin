<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\PopolazioneNomadelfia;
use Carbon;
use Exception;
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
            'persona_id')->whereNull("data_fine")->withPivot('data_inizio')->orderBy('nominativo');
    }

    public function anno()
    {
        return $this->belongsTo(Anno::class, "anno_id", 'id');
    }

    public function tipo()
    {
        return $this->belongsTo(ClasseTipo::class, "tipo_id", 'id');
    }

    public function aggiungiAlunno($alunno, $data_inizio)
    {
        if (is_null($data_inizio)) {
            $data_inizio = $this->anno->data_inizio;
        }
        if (is_string($data_inizio)) {
            $data_inizio = Carbon::parse($data_inizio);
        }
        if (is_integer($alunno)) {
            $alunno = Persona::findOrFail($alunno);
        }
        if ($alunno instanceof Persona) {
            $this->alunni()->attach($alunno->id, [
                'data_inizio' => $data_inizio,
            ]);
        }else {
            throw new Exception("Alunno is not a valid id or model");
        }
    }

    public function rimuoviAlunno($alunno)
    {
        if (is_integer($alunno)) {
            $alunno = Persona::findOrFail($alunno);
        }
        if ($alunno instanceof Persona) {
            $this->alunni()->detach($alunno->id);
        }else {
            throw new Exception("Alunno is not a valid id or model");
        }
    }

    public function alunniPossibili()
    {
        if ($this->tipo->isPrescuola()) {
            $all = PopolazioneNomadelfia::figliDaEta(3, 7, "data_nascita");
        } else {
            $all = PopolazioneNomadelfia::figliDaEta(7, 20, "data_nascita");
        }

        $current = collect($this->anno->alunni());
        $ids = $current->map(function ($item) {
            return $item->id;
        });
        return $all->whereNotIn('id', $ids);
    }

}
