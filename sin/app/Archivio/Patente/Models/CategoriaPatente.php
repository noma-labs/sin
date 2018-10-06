<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;
use App\Patente\Models\Patente;
Use Carbon;

class CategoriaPatente extends Model
{
  protected $connection = 'db_patente';
  protected $table = 'categorie';
  protected $primaryKey = "id";

  public $timestamps = false;
  protected $guarded = [];

  public function patenti(){
      return $this->belongsToMany(Patente::class, 'patenti_categorie','categoria_patente_id','numero_patente')
                  ->withPivot('data_rilascio','data_scadenza');
  }

  /**
   * Ritorna le categorie del C.Q:C persone e C.Q.C merci
   *
   * @author Davide Neri
   */
  public function scopeCQC($query){
    return $query->where('id',16)
                ->orWhere('id',17);
  }

  /**
   * Ritorna la categorie del C.Q:C persone
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
  public function inScadenza($days){
    $data = Carbon::now()->addDays($days)->toDateString();
    return $this->belongsToMany(Patente::class, 'patenti_categorie','categoria_patente_id','numero_patente')
                ->wherePivot('data_scadenza','<=', $data)
                ->wherePivot('data_scadenza',">=",Carbon::now()->toDateString());
  }

  public function scadute($days){
    $data = Carbon::now()->subDays($days)->toDateString();
    return $this->belongsToMany(Patente::class, 'patenti_categorie','categoria_patente_id','numero_patente')
                ->wherePivot('data_scadenza', '>=', $data)
                ->wherePivot('data_scadenza',"<=",Carbon::now()->toDateString());
  }
}