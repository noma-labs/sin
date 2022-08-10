<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Azienda;
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


    public function newEloquentBuilder($query)
    {
        return new ClasseBuilder($query);
    }

    public function alunni($orderby = "nominativo", $order = "ASC")
    {
        return $this->belongsToMany(Persona::class, 'db_scuola.alunni_classi', 'classe_id',
            'persona_id')->whereNull("data_fine")->withPivot('data_inizio')->orderBy($orderby, $order);
    }

    public function coordinatori()
    {
        return $this->belongsToMany(Persona::class, 'db_scuola.coordinatori_classi', 'classe_id',
            'coordinatore_id')->whereNull("data_fine")->withPivot('data_inizio', "tipo")->orderBy('nominativo');
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
        } else {
            throw new Exception("Alunno is not a valid id or model");
        }
    }

    public function importStudentsFromOtherClasse(Classe $classe_from, string $data_inizio)
    {
        $a = $classe_from->alunni()->get();
        $this->alunni()->attach($a, ['data_inizio' => $data_inizio]);
    }

    public function nextClasseTipo()
    {
        return $this->tipo->classeSuccessiva();
    }

    public function aggiungiCoordinatore(Persona $persona, $data_inizio, $tipo = null)
    {
        if (is_null($data_inizio)) {
            $data_inizio = $this->anno->data_inizio;
        }
        if (is_string($data_inizio)) {
            $data_inizio = Carbon::parse($data_inizio);
        }
        if (is_integer($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $attr = ['data_inizio' => $data_inizio];
            if (!is_null($tipo)) {
                $attr['tipo'] = $tipo;
            }
            $this->coordinatori()->attach($persona->id, $attr);
        } else {
            throw new Exception("Coordinatore is not a valid id or model");
        }
    }

    public
    function coordinatoriPossibili()
    {
        $all = Azienda::scuola()->lavoratoriAttuali()->get();

        $current = collect($this->coordinatori()->get());
        $ids = $current->map(function ($item) {
            return $item->id;
        });
        return $all->whereNotIn('id', $ids);
    }

    public
    function rimuoviCoordinatore(
        $coord
    ) {
        if (is_integer($coord)) {
            $coord = Persona::findOrFail($coord);
        }
        if ($coord instanceof Persona) {
            $this->coordinatori()->detach($coord->id);
        } else {
            throw new Exception("Coordinatore  is not a valid id or model");
        }
    }

    public
    function rimuoviAlunno(
        $alunno
    ) {
        if (is_integer($alunno)) {
            $alunno = Persona::findOrFail($alunno);
        }
        if ($alunno instanceof Persona) {
            $this->alunni()->detach($alunno->id);
        } else {
            throw new Exception("Alunno is not a valid id or model");
        }
    }

    public function alunniPossibili()
    {
        $as = $this->anno->annoSolareInizio();
        $tipo = $this->tipo;
        if ($tipo->isPrescuola()) {
//            $all = Studente::FraEta(3, 6, 'nominativo', $as, true)->get();
            $all = PopolazioneNomadelfia::fraEta(3,6,'nominativo', $as,true);
        } elseif ($tipo->IsUniversita()) {
//            $all = Studente::FraEta(18, 26, 'nominativo', $as, true);
            $all = PopolazioneNomadelfia::fraEta(18,26,'nominativo', $as,true);
        } else {
            $all = PopolazioneNomadelfia::fraEta(7, 19, 'nominativo', $as, true);
        }

        $ids = collect(Studente::InAnnoScolastico($this->anno)->get())->pluck('persona_id');
        return $all->whereNotIn('id', $ids);
    }

}
