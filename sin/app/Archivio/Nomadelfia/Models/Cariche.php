<?php

namespace App\Nomadelfia\Models;

use App\Traits\Enums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon;
use Illuminate\Support\Facades\DB;
use \stdClass;


class Cariche extends Model
{
    use Enums;

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';
    protected $table = 'cariche';
    protected $primaryKey = "id";

    protected $enumPosizione = [
        'associazione',
        'solidarieta',
        'fondazione',
        'agricola',
        'culturale'
    ];


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderby('org');
        });
    }

    public static function AssociazioneCariche()
    {
        $membri = self::byOrg("associazione");
        $cariche = $membri->groupBy("nome");
        return $cariche;
    }

    public static function SolidarietaCariche()
    {
        $membri = self::byOrg("solidarieta");
        $cariche = $membri->groupBy("nome");
        return $cariche;
    }

    public static function FondazioneCariche()
    {
        $membri = self::byOrg("fondazione");
        $cariche = $membri->groupBy("nome");
        return $cariche;
    }

    public static function AgricolaCariche()
    {
        $membri = self::byOrg("agricola");
        $cariche = $membri->groupBy("nome");
        return $cariche;
    }

    public static function CulturaleCariche()
    {
        $membri = self::byOrg("culturale");
        $cariche = $membri->groupBy("nome");
        return $cariche;
    }

    public static function byOrg(string $org)
    {
        $membri = DB::connection('db_nomadelfia')
            ->table('cariche')
            ->selectRaw("cariche.*,persone_cariche.*,persone.nominativo, persone.id as persona_id")
            ->leftJoin('persone_cariche', 'cariche.id', '=', 'persone_cariche.cariche_id')
            ->leftJoin('persone', 'persone.id', '=', 'persone_cariche.persona_id')
            ->where("cariche.org", "=", $org)
            ->orderByRaw("cariche.ord")
            ->get();
        // return $query->where("org", "associazione")->orderby("ord");
        // select c.id, c.nome, p.id, p.nome, pc.data_inizio, pc.data_fine
        // from cariche c
        // LEFT JOIN persone_cariche pc on c.id = pc.cariche_id
        // LEFT JOIN persone p on p.id = pc.persona_id
        // where  c.org = 'associazione'
        // order by c.ord
        return $membri;
    }

    public static function EleggibiliConsiglioAnziani()
    {
        $effetivo = Posizione::perNome("effettivo");
        $sacerdote = Stato::perNome("sacerdote");
        $res = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT * 
                FROM persone
                INNER JOIN popolazione ON popolazione.persona_id = persone.id
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                LEFT JOIN persone_stati ON persone_stati.persona_id = persone.id
                WHERE popolazione.data_uscita IS NULL AND persone.stato = '1'
                AND persone.data_nascita <= :date AND persone_posizioni.data_inizio <= :datanoma 
                AND persone_posizioni.posizione_id = :effe AND persone_stati.stato_id != :sac  AND  persone_stati.stato = '1'
                ORDER BY persone.nominativo ASC"
            ),
            array(
                'effe' => $effetivo->id,
                'sac' => $sacerdote->id,
                'date' => Carbon::now()->subYears(40)->toDatestring(),
                'datanoma' => Carbon::now()->subYears(10)->toDatestring()
            )
        );
        $result = new stdClass;
        $maggioreni = collect($res);
        $sesso = $maggioreni->groupBy("sesso");
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }


    // ritorna le persone che ricoprono le cariche di una organizazione
    public function membri()
    {
        return $this->belongsToMany(Persona::class, 'persone_cariche', 'cariche_id',
            'persona_id')->withPivot('data_inizio', 'data_fine');
    }

    public function assegnaMembro($persona, $data_inizio)
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
}

