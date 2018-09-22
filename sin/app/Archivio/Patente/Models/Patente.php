<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;

use App\Nomadelfia\Models\Persona;
use App\Patente\Models\Patente;
use App\Patente\Models\CategoriaPatente;

class Patente extends Model
{
  protected $connection = 'db_patente';
  protected $table = 'persone_patenti';
  protected $primaryKey = "numero_patente";
  public $increment = false;
  public $keyType = 'string';

  public $timestamps = false;
  protected $guarded = [];

  public function persone(){
      return $this->belongsTo(Persona::class, 'persona_id','id');
  }

  public function categorie(){ 
      return $this->belongsToMany(CategoriaPatente::class, 'patenti_categorie','numero_patente','categoria_patente_id')
                                ->withPivot('data_rilascio','data_scadenza','restrizione_codice') ;
  }
}
