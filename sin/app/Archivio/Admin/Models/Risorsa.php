<?php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\Permission;
use Illuminate\Support\Collection;

use App\Admin\Models\Sistema;
use App\Admin\Models\Ruolo;

use App\Admin\Exceptions\RisorsaDoesNotExist;

class Risorsa extends Model
{
    protected $connection = 'db_auth';
    protected $table = 'risorse';
    protected $primaryKey = "id";

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome'
    ];

    public $mapPermessiNames = [
        // permission name used in app => permission name of the column in ruoli_risorse_permessi  table.
        'visualizza' => 'visualizza',
        'inserisci' => 'inserisci',
        'elimina' => 'elimina',
        'modifica' => 'modifica', 
        'prenota' => 'prenota', 
        'esporta' => 'esporta',
        'svuota' => 'svuota' 
    ];

    public function ruoli()
    {
        return $this->belongsToMany(Ruolo::class,'ruoli_risorse_permessi','risorsa_id','ruolo_id');
    }

    public function sistema()
    { // return $this->belongsToMany(Sistema::class,'sistemi_risorse','risorsa_id','sistema_id');
        return $this->belongsTo(Sistema::class,'sistema_id');
    }

    /**
     * Return the roles that have the $permesso=1 to a risorsa.
     * 
     * @param string $permesso
     *
     */
    public function getRuoliForPermesso($permesso){
        return $this->belongsToMany(Ruolo::class,'ruoli_risorse_permessi','risorsa_id','ruolo_id')
                    ->wherePivot($this->mapPermessiNames[$permesso], 1);
    }


    // return a Risorsa find by Name
    public static function findByName(string  $name):Risorsa {

        $risorsa = static::getRisorse()->filter(function ($risorsa) use ($name) {
            return $risorsa->nome === $name;
        })->first();
        if (! $risorsa) {
         throw RisorsaDoesNotExist::create($name);
        }
        return $risorsa;
    } 

    /**
     * Get the current cached risorse.
     */
    protected static function getRisorse(): Collection
    {
        #return app(PermissionRegistrar::class)->getPermissions();
        return Risorsa::with('ruoli')->get();
    }




}
