<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Scuola\Exceptions\GeneralException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ord
 * @property-read Anno $anno
 * @property-read ClasseTipo $tipo
 */
final class Classe extends Model
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
            throw new GeneralException('Alunno is not a valid id or model');
        }
    }

    public function importStudentsFromOtherClasse(self $classe_from, string $data_inizio): void
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
            throw new GeneralException('Coordinatore is not a valid id or model');
        }
    }

    public function coordinatoriPossibili()
    {
        $anno = $this->anno()->first();
        $as = $anno->annoSolareInizio();

        $q = PopolazioneNomadelfia::PresentAt(Carbon::parse($anno->data_inizio))
            ->select('persone.id', 'persone.data_nascita', 'persone.nome', 'persone.cognome', 'persone.nominativo', 'popolazione.data_entrata', 'popolazione.data_uscita')
            ->leftJoin('persone', 'persone.id', '=', 'popolazione.persona_id');

        $date = Carbon::now()->setYear($as);

        $end = $date->copy()->subYears(18)->endOfYear();
        $start = $date->copy()->subYears(100)->startOfYear();

        $all = $q->where('persone.data_nascita', '<=', $end)
            ->where('persone.data_nascita', '>=', $start)
            ->orderByRaw('persone.nome ASC')
            ->get();

        $alreadyIn = $this->coordinatori()->pluck('id');

        return $all->whereNotIn('id', $alreadyIn);
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
            throw new GeneralException('Coordinatore  is not a valid id or model');
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
            throw new GeneralException('Alunno is not a valid id or model');
        }
    }

    public function alunniPossibili()
    {
        $anno = $this->anno()->first();
        $as = $anno->annoSolareInizio();
        $tipo = $this->tipo()->first();

        $date = Carbon::now()->setYear($as);

        if ($tipo->isPrescuola()) {
            $end = $date->copy()->subYears(2)->endOfYear();
            $start = $date->copy()->subYears(7)->startOfYear();
        } elseif ($tipo->isElementari()) {
            $end = $date->copy()->subYears(5)->endOfYear();
            $start = $date->copy()->subYears(13)->startOfYear();
        } elseif ($tipo->isMedie()) {
            $end = $date->copy()->subYears(10)->endOfYear();
            $start = $date->copy()->subYears(20)->startOfYear();
        } elseif ($tipo->IsUniversita()) {
            $end = $date->copy()->subYears(17)->endOfYear();
            $start = $date->copy()->subYears(26)->startOfYear();
        } else {
            $end = $date->copy()->subYears(5)->endOfYear();
            $start = $date->copy()->subYears(25)->startOfYear();

        }

        $all = Persona::query()->select('persone.id', 'persone.data_nascita', 'persone.nome', 'persone.cognome', 'persone.nominativo', 'alfa_enrico_15_feb_23.famiglia as famigliaEnrico')
            ->leftJoin('alfa_enrico_15_feb_23', 'persone.id_alfa_enrico', '=', 'alfa_enrico_15_feb_23.id')
            ->where('persone.data_nascita', '<=', $end)
            ->where('persone.data_nascita', '>=', $start)
            ->orderByRaw('data_nascita ASC, nominativo ASC')
            ->get();

        $ids = Studente::InAnnoScolastico($this->anno)->pluck('persone.id');

        return $all->whereNotIn('id', $ids);
    }
}
