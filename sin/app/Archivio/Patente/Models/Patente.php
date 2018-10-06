<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;

use App\Nomadelfia\Models\Persona;
use App\Patente\Models\Patente;
use App\Patente\Models\CategoriaPatente;
use Carbon;

class Patente extends Model
{
  protected $connection = 'db_patente';
  protected $table = 'persone_patenti';
  protected $primaryKey = "numero_patente";
  public $increment = false;
  public $keyType = 'string';

  public $timestamps = false;
  protected $guarded = [];

  public function persona(){
      return $this->belongsTo(Persona::class, 'persona_id','id');
  }

  public function categorie(){ 
      return $this->belongsToMany(CategoriaPatente::class, 'patenti_categorie','numero_patente','categoria_patente_id')
                                ->withPivot('data_rilascio','data_scadenza') ;
  }

  public function CQCPersone(){
    return $this->belongsToMany(CategoriaPatente::class, 'patenti_categorie','numero_patente','categoria_patente_id')
                                ->withPivot('data_rilascio','data_scadenza')
                                ->wherePivot('categoria_patente_id',16);
  }
  
  /**
   * Ritorna le patenti che scadono entro $days giorni
   * @param int $giorni :numero di giorni entro il quale le patenti scadono.
   * @author Davide Neri
   */
  public function scopeInScadenza($query, int $days){
    $data = Carbon::now()->addDays($days)->toDateString();
    return $query->where('data_scadenza_patente', '<=', $data)
                ->where('data_scadenza_patente',">=",Carbon::now()->toDateString());

  }

   /**
   * Ritorna le patenti che sono già scadute da $giorni.
   * @param int $giorni :numero di giorni di scadenza
   * @author Davide Neri
   */
  public function scopeScadute($query, int $days){
    $data = Carbon::now()->subDays($days)->toDateString();
    return $query->where('data_scadenza_patente', '>=', $data)
                ->where('data_scadenza_patente',"<=",Carbon::now()->toDateString());
  }

  /** Ritorna le patenti a cui è stata assegnata la commissione
   * @author Davide Neri
   */
  public function scopeConCommisione($query){
      return $query->where('stato','=','commissione');
  }
 
   /** Ritorna le patenti senza la  commissione
   * @author Davide Neri
   */
  public function scopeSenzaCommisione($query){
    return $query->whereNull('stato')
                ->orWhere("stato","!=",'commissione');
    }


}
