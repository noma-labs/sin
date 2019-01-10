<?php
namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon;

use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Incarico;
use App\Nomadelfia\Models\Azienda;

use App\Nomadelfia\Exceptions\GruppoFamiliareDoesNOtExists;

use App\Patente\Models\Patente;
use App\Nomadelfia\Models\Stato;
use App\Nomadelfia\Models\Categoria;
use App\Admin\Models\Ruolo;




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
  }

  public function scopeMinorenni($query)
  {
     $date = Carbon::now()->subYears(18)->toDatestring();
     return $query->where('data_nascita', ">", $date);
  }

  public function scopeDonne($query)
  {
    return $query->where('sesso','F');
  }


  public function scopeUomini($query)
  {
     return $query->where('sesso','M');
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

   // GRUPPO FAMILIARE
  public function gruppifamiliari(){
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_persone','persona_id','gruppo_famigliare_id');
  }

  public function gruppofamiliareAttuale(){
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_persone','persona_id','gruppo_famigliare_id')
                 ->wherePivot('stato', '1')
                 ->first();
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
                ->wherePivot('stato', '1')->first();
  }

  public function statoStorico(){
    return $this->belongsToMany(Stato::class, 'persone_stati', 'persona_id', 'stato_id')
                ->wherePivot('stato', '0');
  }

  // FAMIGLIA
  public function famiglie(){
     return $this->belongsToMany(Famiglia::class, 'famiglie_persone', 'persona_id', 'famiglia_id');
  }
  public function famigliaAttuale(){
    return $this->famiglie()
                ->wherePivot('stato', '1')
                ->withPivot('posizione_famiglia')
                ->first();
  }
  
  public function famiglieStorico(){
    return $this->famiglie()
                ->wherePivot('stato', '0')
                ->withPivot('posizione_famiglia');
  }

  /**
   * Ritorna la posizione di una persona in una famiglia
   * @param String $posizione 
   * @return boolean  
   * @author Davide Neri
   **/
  public function famigliaPosizione(string $posizione){
    if($this->famigliaAttuale())
        return $this->famigliaAttuale()->pivot->posizione_famiglia == $posizione;
    else
      return false;
  }

  /**
   * Ritorna vero se la persona è single altrimenti ritorna falso.
   * @return boolean  
   * @author Davide Neri
   **/
  public function isSingle(){
    return  $this->famigliaPosizione("SINGLE");
  }

  /**
   * Ritorna vero se una persona è  il capo famiglia altrimenti ritorna falso.
   * @return boolean  
   * @author Davide Neri
   **/
  public function isCapoFamiglia(){
    return  $this->famigliaPosizione("CAPO FAMIGLIA");
  }

  /**
   * Ritorna vero se una persona è la moglie altrimenti ritorna falso.
   * @return boolean  
   * @author Davide Neri
   **/
  public function isMoglie(){
    return  $this->famigliaPosizione("MOGLIE");
  }

   /**
   * Ritorna vero se una persona è un figlioaccolto altrimenti ritorna falso.
   * @return boolean  
   * @author Davide Neri
   **/
  public function isFiglio(){
    return  $this->isFiglioNato() or $this->isFiglioAccolto();
  }

  /**
   * Ritorna vero se una persona è un figlio nato altrimenti ritorna falso.
   * @return boolean  
   * @author Davide Neri
   **/
  public function isFiglioNato(){
    return  $this->famigliaPosizione("FIGLIO NATO");
  }

  /**
   * Ritorna vero se una persona è un figlioaccolto altrimenti ritorna falso.
   * @return boolean  
   * @author Davide Neri
   **/
  public function isFiglioAccolto(){
    return  $this->famigliaPosizione("FIGLIO ACCOLTO");
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

  /**
     * Sposta una persona e la sua famiglia dal gruppo familiare attuale in un nuovo gruppo familiare.
     *
     * @param int|null $gruppoFamiliareAttuale
     * @param date   $dataUscitaGruppoFamiliareAttuale
     * @param int $gruppoFamiliareNuovo
     * @param date $dataEntrataGruppo
     *
     */
  public function cambiaGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale=null, $gruppoFamiliareNuovo, $dataEntrataGruppo=null){
    if($this->isCapoFamiglia()){
        $this->famigliaAttuale()->assegnaFamigliaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale, $gruppoFamiliareNuovo, $dataEntrataGruppo);
    }
  }

  public function assegnaPersonaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale=null, $gruppoFamiliareNuovo, $dataEntrataGruppo=null){
      $this->gruppifamiliari()->updateExistingPivot($gruppoFamiliareAttuale,['stato' => '0','data_uscita_gruppo'=>$dataUscitaGruppoFamiliareAttuale]);
      $this->gruppifamiliari()->attach($gruppoFamiliareNuovo, ['stato' => '1','data_entrata_gruppo'=>$dataEntrataGruppo]);
  }
}
