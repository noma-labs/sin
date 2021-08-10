<?php

namespace App\Nomadelfia\Models;

use App\Traits\Enums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon;
use Illuminate\Support\Facades\DB;


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
        //        return $query->where("org", "associazione")->orderby("ord");
        //        select c.id, c.nome, p.id, p.nome, pc.data_inizio, pc.data_fine
        //from cariche c
        //LEFT JOIN persone_cariche pc on c.id = pc.cariche_id
        //LEFT JOIN persone p on p.id = pc.persona_id
        //where  c.org = 'associazione'
        //order by c.ord
        return $membri;
    }

    public function scopePresidente($query)
    {
        return $query->where("nome", "Presidente");
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

