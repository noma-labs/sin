<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;
use App\Patente\Models\Patente;
use App\Patente\Models\Restrizione;


class CategoriaPatenteRestrizione extends Pivot
{
  protected $connection = 'db_patente';
  protected $table = 'patenti_categorie_restrizioni';
  // protected $primaryKey = "id";

  public $timestamps = false;
  protected $guarded = [];

  public function restrizioni(){
      return $this->belongsToMany(Restrizione::class, 'patenti_categorie','categoria_patente_id','numero_patente')
  }
}