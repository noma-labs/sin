<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;
use App\Patente\Models\Patente;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\SortableTrait;
Use Carbon;

class CQC extends Model
{
  use SortableTrait;
  
  protected $connection = 'db_patente';
  protected $table = 'categorie';

  public $timestamps = false;
  protected $guarded = [];


  /**
   * Define a global scope for obtaining only the CQC among all the actegorie.
   */
  protected static function boot(){
        parent::boot();
        static::addGlobalScope('id', function (Builder $builder) {
            $builder->where('id', 16)->orWhere('id',17);
        });
  }

  public function patenti(){
      return $this->belongsToMany(Patente::class, 'patenti_categorie','categoria_patente_id','numero_patente')
                  ->withPivot('data_rilascio','data_scadenza');
  }

  
  /**
   * Ritorna il C.Q:C persone
   *
   * @author Davide Neri
   */
  public function scopeCQCPersone($query){
    return $query->where('id',16)->first();
  }

  
  /**
   * Ritorna la categorie del C.Q:C merci
   *
   * @author Davide Neri
   */
  public function scopeCQCMerci($query){
    return $query->where('id',17)->first();
  }

  /**
   * Ritorna le patenti che scadono entro $days giorni
   * @param int $days :numero di giorni entro il quale le patenti scadono.
   * @author Davide Neri
   */
  public function scopeinScadenza($query,$days){
    $data = Carbon::now()->addDays($days)->toDateString();
    return $this->belongsToMany(Patente::class, 'patenti_categorie','categoria_patente_id','numero_patente')
                ->withPivot('data_rilascio','data_scadenza')
                ->wherePivot('data_scadenza','<=', $data)
                ->wherePivot('data_scadenza',">=",Carbon::now()->toDateString());
  }

  /**
   * Ritorna le patenti con C.Q.C che non sono in scadenza da $days giorni in poi.
   * @param int $giorni: numero di giorni entro il quale le patenti scadono.
   * @author Davide Neri
   */
  public function scopeNonInScadenza($query, int $days){
    $data = Carbon::now()->addDays($days)->toDateString();
    return $this->belongsToMany(Patente::class, 'patenti_categorie','categoria_patente_id','numero_patente')
                ->withPivot('data_rilascio','data_scadenza')
                ->wherePivot('data_scadenza','>', $data);

  }
  
  public function scadute($days){
    $data = Carbon::now()->subDays($days)->toDateString();
    return $this->belongsToMany(Patente::class, 'patenti_categorie','categoria_patente_id','numero_patente')
                ->withPivot('data_rilascio','data_scadenza')
                ->wherePivot('data_scadenza', '>=', $data)
                ->wherePivot('data_scadenza',"<=",Carbon::now()->toDateString());
  }
}