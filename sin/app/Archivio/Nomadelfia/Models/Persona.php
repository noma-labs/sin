<?php
namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;

use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Incarico;
use App\Nomadelfia\Models\Azienda;
use App\Admin\Models\Ruolo;

class Persona extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'persone';
  protected $primaryKey = "id";

  public $timestamps = false;
  protected $guarded = [];

  public function gruppi(){
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_persone','persona_id','gruppo_famigliare_id');
  }

  public function famiglie(){
    return $this->belongsToMany(Famiglia::class,'famiglie_persone','persona_id','famiglia_id')
                    ->withPivot('nucleo_famigliare_id');
  }

  public function scopeCapifamiglia()
  {
      // return $this->belongsToMany(Famiglia::class,'famiglie_persone','persona_id','famiglia_id')
      //               ->wherePivot('cf','AG')->toSql();
      return $this->whereHas('famiglie', function($q){
        $q->where("cf","CF");
      });

  }

  public function nucleiFamiliari(){
    return $this->belongsToMany(NucleoFamigliare::class,'famiglie_persone','persona_id','nucleo_famigliare_id');
  }

  public function scopeSingoli(){
    return $this->whereHas('famiglie', function($q){
      $q->where("nucleo_famigliare_id",7);
    });
  }

  public function posizioni(){
    return $this->belongsToMany(Posizione::class, 'persone_posizioni', 'persona_id', 'posizione_id');
  }

  public function aziende(){
    return $this->belongsToMany(Azienda::class, 'aziende_persone', 'persona_id', 'azienda_id');
  }

  public function incarichi(){
    return $this->belongsToMany(Incarico::class, 'organi_constituzionali_persone', 'persona_id', 'organo_constituzionale_id');
  }
  /**
   * Returns only the people that are currently living in Nomadelfia.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopePresente($query)
  {
      return $query->where('incarico_id', ">",0);
  }

  


}
