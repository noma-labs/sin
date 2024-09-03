<?php

namespace App\Scuola\Models;

use Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ord
 */
class Classe extends Model
{
    public $timestamps = true;

    protected $connection = 'db_scuola';

    protected $table = 'classi';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function newEloquentBuilder($query)
    {
        return new ClasseBuilder($query);
    }

    public function alunni($orderby = 'sesso  DESC, nominativo ASC')
    {
        return $this->belongsToMany(Persona::class, 'db_scuola.alunni_classi', 'classe_id',
            'persona_id')->withPivot('data_inizio')->orderByRaw($orderby);
    }

    public function alunniAttuali()
    {
        return $this->alunni()->whereNull('data_fine');
    }

    public function coordinatori()
    {
        return $this->belongsToMany(Persona::class, 'db_scuola.coordinatori_classi', 'classe_id',
            'coordinatore_id')->whereNull('data_fine')->withPivot('data_inizio', 'tipo')->orderBy('nominativo');
    }

    public function anno(): BelongsTo
    {
        return $this->belongsTo(Anno::class, 'anno_id', 'id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ClasseTipo::class, 'tipo_id', 'id');
    }

    public function aggiungiAlunno($alunno, $data_inizio): void
    {
        if (is_null($data_inizio)) {
            $a = $this->anno()->first();
            $data_inizio = $a->data_inizio;
        }
        if (is_string($data_inizio)) {
            $data_inizio = Carbon::parse($data_inizio);
        }
        if (is_int($alunno)) {
            $alunno = Persona::findOrFail($alunno);
        }
        if ($alunno instanceof Persona) {
            $this->alunni()->attach($alunno->id, [
                'data_inizio' => $data_inizio,
            ]);
        } else {
            throw new Exception('Alunno is not a valid id or model');
        }
    }

    public function importStudentsFromOtherClasse(Classe $classe_from, string $data_inizio): void
    {
        $a = $classe_from->alunni()->get();
        $this->alunni()->attach($a, ['data_inizio' => $data_inizio]);
    }

    public function nextClasseTipo()
    {
        return $this->tipo->classeSuccessiva()->first();
    }

    public function aggiungiCoordinatore(Persona|int $persona, $data_inizio, $tipo = null): void
    {
        if (is_null($data_inizio)) {
            $data_inizio = $this->anno()->first()->data_inizio;
        }
        if (is_string($data_inizio)) {
            $data_inizio = Carbon::parse($data_inizio);
        }
        if (is_int($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $attr = ['data_inizio' => $data_inizio];
            if (! is_null($tipo)) {
                $attr['tipo'] = $tipo;
            }
            $this->coordinatori()->attach($persona->id, $attr);
        } else {
            throw new Exception('Coordinatore is not a valid id or model');
        }
    }

    public function coordinatoriPossibili()
    {
        $all = Azienda::scuola()->lavoratoriAttuali()->get();

        $current = collect($this->coordinatori()->get());
        $ids = $current->map(function ($item) {
            return $item->id;
        });

        return $all->whereNotIn('id', $ids);
    }

    public function rimuoviCoordinatore(
        $coord
    ): void {
        if (is_int($coord)) {
            $coord = Persona::findOrFail($coord);
        }
        if ($coord instanceof Persona) {
            $this->coordinatori()->detach($coord->id);
        } else {
            throw new Exception('Coordinatore  is not a valid id or model');
        }
    }

    public function rimuoviAlunno(
        $alunno
    ): void {
        if (is_int($alunno)) {
            $alunno = Persona::findOrFail($alunno);
        }
        if ($alunno instanceof Persona) {
            $this->alunni()->detach($alunno->id);
        } else {
            throw new Exception('Alunno is not a valid id or model');
        }
    }

    public function alunniPossibili()
    {
        $as = $this->anno()->first()->annoSolareInizio();
        $tipo = $this->tipo()->first();
        if ($tipo->isPrescuola()) {
            $all = PopolazioneNomadelfia::fraEta(3, 6, 'data_nascita', $as, true, 'DESC')->get();
        } elseif ($tipo->IsUniversita()) {
            $all = PopolazioneNomadelfia::fraEta(17, 26, 'data_nascita', $as, true, 'DESC')->get();
        } else {
            $all = PopolazioneNomadelfia::fraEta(6, 25, 'data_nascita', $as, true, 'DESC')->get();
        }

        $ids = collect(Studente::InAnnoScolastico($this->anno)->get())->pluck('persona_id');

        return $all->whereNotIn('id', $ids);
    }
}
