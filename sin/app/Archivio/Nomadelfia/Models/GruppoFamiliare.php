<?php

namespace App\Nomadelfia\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Famiglia;

class GruppoFamiliare extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'gruppi_familiari';
  protected $primaryKey = "id";

  public function persone()
  {
    return $this->belongsToMany(Persona::class,'gruppi_persone','gruppo_famigliare_id','persona_id');
  }

  public function famiglie()
  {
    return $this->belongsToMany(Famiglia::class,'gruppi_famiglie','gruppo_famigliare_id','famiglia_id');
  }

  public function capogruppi()
  {
    return $this->belongsToMany(Persona::class,'gruppi_familiari_capogruppi','gruppo_familiare_id','persona_id');
  }

  public function capogruppiAttuali()
  {
    return $this->belongsToMany(Persona::class,'gruppi_familiari_capogruppi','gruppo_familiare_id','persona_id')
                ->wherePivot('stato',1);
  }

  public static function getCountNucleiFamiliari(){
    //ritorna la statistica di quanti SINGLE, FIGLI naturali, Fagli affidati , padri e madri ci sono nel gruppo.
    $r = DB::connection('db_nomadelfia')
        ->select(DB::raw(
            'SELECT gruppi_famiglie.gruppo_famigliare_id, nuclei_famigliari.nucleo_famigliare, count(2) as count 
            FROM nuclei_famigliari,famiglie_persone,gruppi_famiglie 
            WHERE famiglie_persone.nucleo_famigliare_id=nuclei_famigliari.id  
            AND famiglie_persone.famiglia_id =gruppi_famiglie.famiglia_id 
            GROUP BY gruppi_famiglie.gruppo_famigliare_id,nuclei_famigliari.nucleo_famigliare'));
       
   return $r;
  }
}
