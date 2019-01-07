<?php
namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon;

use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Incarico;
use App\Nomadelfia\Models\Azienda;
use App\Patente\Models\Patente;
use App\Nomadelfia\Models\Stato;
use App\Nomadelfia\Models\Categoria;
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
   * Set the nominativo in uppercase when a new persona is insereted.
   */
  public function setNominativoAttribute($value) {
      $this->attributes['nominativo'] = strtoupper($value);
  }

  /**
   * Returns only the people that are currently living in Nomadelfia.
   */
  public function scopePresente($query)
  {
      return $query->where('categoria_id', "<", 4);
  }

  public function scopeMaggiorenni($query)
  {
     $date = Carbon::now()->subYears(18)->toDatestring();
     return $query->where('data_nascita', "<=", $date);
  
    //  return $this->hasOne(DatiPersonali::class, 'persona_id', 'id')->Maggiorenni();
    // return $query->whereHas('datipersonali', function($q){
    //     $q->Maggiorenni();
    // });
    return $query::where('id', function($query){
          $date = Carbon::now()->subYears(18)->toDatestring();
            $query->select('paper_type_id')
            ->from(with(new datiPersonali)->getTable())
            ->whereIn('data_nascita', $date);
        });
    // return $query->whereHas('datipersonali', function($q){
    //   $date = Carbon::now()->subYears(18)->toDatestring();
    //   $q->where('data_nascita', "<=", $date);
    // });
  }

  public function scopeMinorenni($query)
  {
     $date = Carbon::now()->subYears(18)->toDatestring();
     return $query->where('data_nascita', ">", $date);
  }

  public function scopeDonne($query)
  {
    return $query->where('sesso','F');
    // return $this::datiPersonali->where('sesso', 'M');
    // return $query->where(->donne();
    
  // });
    // return $this->datiPersonali()->where('sesso','M');
  }


  public function scopeUomini($query)
  {
     return $query->where('sesso','M');
    //  $users = Persona::with(['datipersonali' => function ($query) {
    //     $query->where('sesso', 'M');
    //   }])->get();
  }

  
   /**
   * Ritorna le persone che hanno gli anni maggiori o uguali di $eta. 
   * @author Davide Neri
   **/
  public function scopeDaEta($query, int $eta){
    $data = Carbon::now()->subYears($eta)->toDateString();
    return $query->where('data_nascita', '<=', $data);
  }

   /**
   * Ritorna le persone che hanno gli anno da $frometa fino a $toeta. 
   * @author Davide Neri
   **/
  public function scopeFraEta($query, int $frometa, int $toeta){
    $fromdata = Carbon::now()->subYears($toeta)->toDateString();
    $todata = Carbon::now()->subYears($frometa)->toDateString();
    return $query->whereBetween('data_nascita',[$fromdata, $todata]);
  }
 
  public function patenti(){
    return $this->hasMany(Patente::class, 'persona_id', 'id');
  }

  public function categoria(){
    return $this->hasOne(Categoria::class,  'id', 'categoria_id');
  }

  public function nominativiStorici(){
    return $this->hasOne(NominativoStorico::class, 'persona_id', 'id');
  }


  /**
   * Ritorna i dati personali  della persona 
   * @author Davide Neri
   **/
  public function datiPersonali(){
    return $this->hasOne(DatiPersonali::class,  'persona_id', 'id');
  }

   // GRUPPO FAMILIARE
   public function gruppofamiliareAttuale(){
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_persone','persona_id','gruppo_famigliare_id')
                 ->wherePivot('stato', '1')->first();
  }

  public function gruppofamiliariStorico(){
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_persone','persona_id','gruppo_famigliare_id')
                  ->wherePivot('stato', '0');
  }
 
  // AZIENDE
  public function aziendeAttuali(){
    return $this->belongsToMany(Azienda::class, 'aziende_persone', 'persona_id', 'azienda_id')
              ->wherePivotIn('stato', ['Attivo', 'Sospeso'])
              ->withPivot('data_inizio_azienda', 'mansione', 'stato');
              // ->orderBy('nominativo');
  }

  public function aziende(){
    return $this->belongsToMany(Azienda::class, 'aziende_persone', 'persona_id', 'azienda_id');
  }


  // STATO 
  public function statoAttuale(){
    return $this->belongsToMany(Stato::class, 'persone_stati', 'persona_id', 'stato_id')
                ->wherePivot('stato', 1)->first();
  }

  public function statoStorico(){
    return $this->belongsToMany(Stato::class, 'persone_stati', 'persona_id', 'stato_id')
                ->wherePivot('stato', 0);
  }

  // FAMIGLIA
  public function famigliaAttuale(){
    return $this->belongsToMany(Famiglia::class, 'famiglie_persone', 'persona_id', 'famiglia_id')
                ->wherePivot('stato', '1')->first();
  }
  
  public function famigliaStorico(){
    return $this->belongsToMany(Famiglia::class, 'famiglie_persone', 'persona_id', 'famiglia_id')
                ->wherePivot('stato', '0');
  }

  // POSIZIONE
  public function posizioneAttuale(){
    return $this->belongsToMany(Posizione::class, 'persone_posizioni', 'persona_id', 'posizione_id')
                ->wherePivot('stato', '1')->first();
  }

  public function posizioneStorica(){
    return $this->belongsToMany(Posizione::class, 'persone_posizioni', 'persona_id', 'posizione_id')
                ->wherePivot('stato', '0');
  }
   
  //INCARICHI
  public function incarichiAttuali(){
    return $this->belongsToMany(Incarico::class, 'organi_constituzionali_persone', 'persona_id', 'organo_constituzionale_id')
                ->wherePivot('stato', '1');
  }

  public function incarichiStorici(){
    return $this->belongsToMany(Incarico::class, 'organi_constituzionali_persone', 'persona_id', 'organo_constituzionale_id')
                ->wherePivot('stato', '0');
  }
}
