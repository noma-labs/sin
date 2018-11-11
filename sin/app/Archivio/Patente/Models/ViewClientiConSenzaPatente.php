<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;

class ViewClientiConSenzaPatente extends Model
{
    protected $table = 'clienti_con_senza_patenti';
    protected $connection = 'db_patente';
    protected $primaryKey = "persona_id";
  
  /**
   * Ritorna  clienti che hanno la patente
   *
   * @author Davide Neri
   */
  public function scopeConPatente($query){
    return $query->where('cliente_con_patente',"CP");
  }

  /**
   * Ritorna  clienti che hanno la patente
   *
   * @author Davide Neri
   */
  public function scopeSenzaPatente($query){
    return $query->where('cliente_con_patente','!=', "CP")->orWhereNull('cliente_con_patente');
  }

  public function patenti(){
    return $this->hasMany(Patente::class, 'persona_id', 'persona_id');
  }

}
