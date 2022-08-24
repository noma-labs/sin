<?php
namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;

use App\Auth\Models\User as User;
use Domain\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\Models\ViewLavoratoriBiblioteca;
use App\Biblioteca\Models\Libro as Libro;

class Prestito extends Model
{
  protected $connection = 'db_biblioteca';
  protected $table = 'prestito';
  protected $primaryKey = "id";

  protected $guarded = ["id"];

  public function cliente()
  {  
    return $this->hasOne(Persona::class,'id','cliente_id');
  }

  public function bibliotecario(){
    return $this->belongsTo(Persona::class,"bibliotecario_id",'id'); //->withTrashed();
  }

  public function libro(){
    return $this->belongsTo(Libro::class,"libro_id")->withTrashed();
  }

  public function scopeInPrestito($query)
  {
    return $query->where('in_prestito', 1);
  }

  public function scopeRestituiti($query)
  {
    return $query->where('in_prestito', 0);
  }

}
