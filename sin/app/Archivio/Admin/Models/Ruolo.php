<?php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\Permission;

use App\Admin\Models\Risorsa;
use App\Admin\Models\User;

class Ruolo extends Model
{
    protected $connection = 'db_auth';
    protected $table = 'ruoli';
    protected $primaryKey = "id";

    public $timestamps = false;

    public $guarded = ['id'];
    
    // The attributes that are mass assignable.
    protected $fillable = [
        'nome'
    ];

    public function risorse(){
        return $this->belongsToMany(Risorsa::class,'ruoli_risorse_permessi','ruolo_id','risorsa_id')
                    ->withPivot('visualizza', 'inserisci','elimina','modifica','prenota','esporta','svuota')
                    ->as('permessi');
    }
    
    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function utenti()
    {
        return $this->belongsToMany(
            User::class,
            'utenti_ruoli',
            'ruolo_id',
            'utente_id'
        );
    }

    public static function findByName($name){
        $ruolo = Ruolo::all()->filter(function ($ruolo) use ($name) {
            return $ruolo->nome === $name;
        })->first();
        if (! $ruolo) {
         throw ruoloDoesNotExist::create($name, $guardName);
        }
        return $ruolo;
    }
}
