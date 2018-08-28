<?php
namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon;

use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Incarico;
use App\Nomadelfia\Models\Azienda;
use App\Admin\Models\Ruolo;
use App\Anagrafe\Models\DatiPersonali;


class Persona extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'persone';
  protected $primaryKey = "id";

  public $timestamps = false;
  protected $guarded = [];

  
  /**
   * Returns only the people that are currently living in Nomadelfia.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopePresente($query)
  {
      return $query->where('categoria_id', "<",4);
  }

   // Persona::pereta()->donne()
  public function scopeMaggiorenni($query)
  {
     $date = Carbon::now()->subYears(18)->toDatestring();
     return $query->where('data_nascita_persona', "<=", $date);
  }

  public function scopeMinorenni($query)
  {
     $date = Carbon::now()->subYears(18)->toDatestring();
     return $query->where('data_nascita_persona', ">", $date);
  }

  public function scopeDonne($query)
  {
     return $query->where('sesso','F');
  }

  public function scopeUomini($query)
  {
     return $query->where('sesso','M');
  }

  public function gruppi(){
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_persone','persona_id','gruppo_famigliare_id');
  }
 
  public function posizioni(){
    return $this->belongsToMany(Posizione::class, 'persone_posizioni', 'persona_id', 'posizione_id');
  }

  public function aziende(){
    return $this->belongsToMany(Azienda::class, 'aziende_persone', 'persona_id', 'azienda_id');
  }

  public function aziendeAttuali(){
    return $this->belongsToMany(Azienda::class, 'aziende_persone', 'persona_id', 'azienda_id')->wherePivotIn('stato', ['Attivo', 'Sospeso'])->withPivot('data_inizio_azienda', 'mansione', 'stato')->orderBy('nominativo');
  }

  public function incarichi(){
    return $this->belongsToMany(Incarico::class, 'organi_constituzionali_persone', 'persona_id', 'organo_constituzionale_id');
  }

  


  public function scopeDaEta($query, $eta){
    $data = Carbon::now()->subYears($eta)->toDateString();
    return $query->where('data_nascita_persona', '<=', $data);
  }


}
