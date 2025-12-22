<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Nomadelfia\Exceptions\CouldNotAssignCarica;
use App\Nomadelfia\Persona\Models\Persona;
use App\Traits\Enums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use stdClass;

final class Cariche extends Model
{
    use Enums;

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';

    protected $table = 'cariche';

    protected $primaryKey = 'id';

    protected $enumPosizione = [
        'associazione',
        'solidarieta',
        'fondazione',
        'agricola',
        'culturale',
    ];

    public static function AssociazioneCariche()
    {
        $membri = self::byOrg('associazione');

        return $membri->get()->groupBy('nome');
    }

    public static function GetAssociazionePresidente()
    {
        $membri = self::byOrg('associazione');

        return $membri->select('persone.*')
            ->where('cariche.nome', '=', 'Presidente')
            ->first();
    }

    public static function SolidarietaCariche()
    {
        $membri = self::byOrg('solidarieta');

        return $membri->get()->groupBy('nome');
    }

    public static function FondazioneCariche()
    {
        $membri = self::byOrg('fondazione');

        return $membri->get()->groupBy('nome');
    }

    public static function AgricolaCariche()
    {
        $membri = self::byOrg('agricola');

        return $membri->get()->groupBy('nome');
    }

    public static function CulturaleCariche()
    {
        $membri = self::byOrg('culturale');

        return $membri->get()->groupBy('nome');
    }

    public static function byOrg(string $org)
    {
        return DB::connection('db_nomadelfia')
            ->table('cariche')
            ->selectRaw('cariche.*,persone_cariche.*,persone.nominativo, persone.id as persona_id')
            ->leftJoin('persone_cariche', 'cariche.id', '=', 'persone_cariche.cariche_id')
            ->leftJoin('persone', 'persone.id', '=', 'persone_cariche.persona_id')
            ->where('cariche.org', '=', $org)
            ->whereNull('persone_cariche.data_fine')
            ->orderByRaw('cariche.ord');
    }

    public static function EleggibiliConsiglioAnziani(): stdClass
    {
        $effetivo = Posizione::perNome('effettivo');
        $sacerdote = Stato::perNome('sacerdote');

        $expression = DB::raw(
            'SELECT *
                FROM persone
                INNER JOIN popolazione ON popolazione.persona_id = persone.id
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                LEFT JOIN persone_stati ON persone_stati.persona_id = persone.id
                WHERE popolazione.data_uscita IS NULL
                AND persone.data_nascita <= :date AND persone_posizioni.data_inizio <= :datanoma
                AND persone_posizioni.posizione_id = :effe AND persone_stati.stato_id != :sac
                ORDER BY persone.nominativo ASC'
        );
        $res = DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            [
                'effe' => $effetivo->id,
                'sac' => $sacerdote->id,
                'date' => \Illuminate\Support\Facades\Date::now()->subYears(40)->toDatestring(),
                'datanoma' => \Illuminate\Support\Facades\Date::now()->subYears(10)->toDatestring(),
            ]
        );
        $result = new stdClass;
        $maggioreni = collect($res);
        $sesso = $maggioreni->groupBy('sesso');
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    public function assegnaPresidenteAssociazione($persona, Carbon $data_inizio)
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $pres = self::GetAssociazionePresidente();
            if ($pres !== null) {
                throw CouldNotAssignCarica::presidenteAssociazioneAlreadySet($pres);
            }

            return $this->assegnaMembro($persona, $data_inizio);
        }
        throw new InvalidArgumentException("Identificativo `{$persona}` della persona non valido.");
    }

    public function membri()
    {
        return $this->belongsToMany(Persona::class, 'persone_cariche', 'cariche_id',
            'persona_id')->withPivot('data_inizio', 'data_fine');
    }

    public function assegnaMembro($persona, $data_inizio): void
    {
        if (is_string($persona)) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona instanceof Persona) {
            $this->membri()->attach($persona->id, [
                'data_inizio' => $data_inizio,
            ]);
        }
    }

    protected static function boot(): void
    {
        parent::boot();

        self::addGlobalScope('order', function (Builder $builder): void {
            $builder->orderby('org');
        });
    }

    protected function scopeAssociazione($query)
    {
        return $query->where('org', '=', 'associazione');
    }

    protected function scopePresidente($query)
    {
        return $query->where('nome', '=', 'presidente');
    }
}
