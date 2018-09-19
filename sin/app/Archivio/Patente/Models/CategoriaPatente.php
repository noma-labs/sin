<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;
use App\Patente\Models\Patente;

class CategoriaPatente extends Model
{
  protected $connection = 'db_patente';
  protected $table = 'categorie';
  protected $primaryKey = "id";

  public $timestamps = false;
  protected $guarded = [];

  public function patenti(){
      return $this->belongsToMany(Patente::class, 'patenti_categorie','categoria_patente_id','numero_patente');
  }
}