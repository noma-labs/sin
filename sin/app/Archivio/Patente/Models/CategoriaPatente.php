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
                  ->withPivot('data_rilascio','data_scadenza','restrizione_codice');
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