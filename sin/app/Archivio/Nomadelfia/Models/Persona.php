<?php
namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
Use Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Traits\SortableTrait;

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

  use SortableTrait;


  protected $connection = 'db_nomadelfia';
  protected $table = 'persone';
  protected $primaryKey = "id";

  public $timestamps = true;
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

  public function scopeAttivo($query)
  {
      return $query->where('persone.stato', '1');
  }


  public function isAttivo()
  {
      return $this->stato == "1";
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
   * 
   * @param int $eta 
   * @author Davide Neri
   **/
  public function scopeDaEta($query, int $eta){
    $data = Carbon::now()->subYears($eta)->toDateString();
    return $query->where('data_nascita', '<=', $data);
  }

   /**
   * Ritorna le persone che hanno un eta compresa tra da $frometa e  $toeta.
   * @param int $frometa
   * @param int $toeta 
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

  public function nominativiStorici(){
    return $this->hasOne(NominativoStorico::class, 'persona_id', 'id');
  }

   // GRUPPO FAMILIARE
  public function gruppifamiliari(){
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_persone','persona_id','gruppo_famigliare_id')
                ->withPivot("data_entrata_gruppo","data_uscita_gruppo",'stato');
  }

  public function gruppofamiliareAttuale(){
    return $this->gruppifamiliari()
                 ->wherePivot('stato', '1')
                 ->get();
  }

  public function gruppofamiliariStorico(){
    return $this->gruppifamiliari()
                ->wherePivot('stato', '0');
  }

  public function concludiGruppoFamiliare($gruppo_id, $datain, $dataout){
    $res = DB::connection('db_nomadelfia')->update( 
      DB::raw("UPDATE gruppi_persone
               SET stato = '0', data_uscita_gruppo = :dataout
               WHERE gruppo_famigliare_id  = :gruppo AND persona_id = :persona AND data_entrata_gruppo = :datain"),
               array('persona' => $this->id, "gruppo"=> $gruppo_id, "datain"=>$datain, "dataout"=>$dataout)
      );
    return $res;
  }

  public function updateDataInizioGruppoFamiliare($gruppo_id, $currentDatain, $newDataIn){
    $res = DB::connection('db_nomadelfia')->update( 
      DB::raw("UPDATE gruppi_persone
               SET  data_entrata_gruppo = :new
               WHERE gruppo_famigliare_id  = :gruppo AND persona_id = :persona AND data_entrata_gruppo = :current"),
               array('persona' => $this->id, "gruppo"=> $gruppo_id, "current"=>$currentDatain, "new"=>$newDataIn)
      );
    return $res;
  }
 
  // AZIENDE
  public function aziende(){
    return $this->belongsToMany(Azienda::class, 'aziende_persone', 'persona_id', 'azienda_id')
                ->withPivot('data_inizio_azienda', 'data_fine_azienda','mansione', 'stato')
                ->orderby("nome_azienda");
  }

  public function aziendeAttuali(){
    return $this->aziende()->wherePivotIn('stato', ['Attivo', 'Sospeso']);
  }

  public function aziendeStorico(){
    return $this->aziende()->wherePivot('stato', 'Non attivo');
  }

 

  // CATEGORIA
  public function categorie(){
    return $this->belongsToMany(Categoria::class, 'persone_categorie', 'persona_id', 'categoria_id')
                ->withPivot('stato','data_inizio','data_fine');
  }

  public function categoriaAttuale(){
    return $this->categorie()->wherePivot('stato', '1')->first();
  }

  public function categorieStorico(){
    return $this->categorie()->wherePivot('stato', '0')
                ->orderby('data_fine','desc');
  }

  /**
   * Ritorna le posizioni assegnabili ad una persona.
   * @return Collection Posizione 
   * @author Davide Neri
   **/
  public function categoriePossibili(){
    $categoria = self::categoriaAttuale();
    $categorie = Categoria::all();
    if($categoria != null){
      $categorie = $categorie->except([$categoria->id]);
      // if($categoria->is(Posizione::findByName("EFFETTIVO")))
      //   return $categorie->except([Posizione::findByName("FIGLIO")->id]);
      // if($categoria->is(Posizione::findByName("POSTULANTE")))
      //   return $categorie->except([Posizione::findByName("FIGLIO")->id]);
      // if($categoria->is(Posizione::findByName("OSPITE")))
      //   return $categorie->except([Posizione::findByName("EFFETTIVO")->id]);
      // if($categoria->is(Posizione::findByName("FIGLIO")))
      //   return $categorie->except([Posizione::findByName("EFFETTIVO")->id]);
      return $categorie;
    }else
    return $categorie;
  }

  // STATO 
  public function stati(){
    return $this->belongsToMany(Stato::class, 'persone_stati', 'persona_id', 'stato_id')
                ->withPivot('stato','data_inizio','data_fine');
  }
  public function statoAttuale(){
    return $this->stati()->wherePivot('stato', '1')->first();
  }

  public function statiStorico(){
    return $this->stati()->wherePivot('stato', '0')
                ->orderby('data_fine','desc');
  }

  // FAMIGLIA
  public function famiglie(){
     return $this->belongsToMany(Famiglia::class, 'famiglie_persone', 'persona_id', 'famiglia_id')
                  ->withPivot("stato");
  }

  public function famigliaAttuale(){
    return $this->famiglie()
                ->wherePivot('stato', '1')
                ->withPivot('posizione_famiglia','data_entrata', "data_uscita")
                ->first();
  }
  
  public function famiglieStorico(){
    return $this->famiglie()
                ->wherePivot('stato', '0')
                ->withPivot('posizione_famiglia','data_entrata', "data_uscita");
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
  public function posizioni(){
    return $this->belongsToMany(Posizione::class, 'persone_posizioni', 'persona_id', 'posizione_id')
                 ->withPivot('stato','data_inizio','data_fine');
  }
  public function posizioneAttuale(){
    return $this->posizioni()
                ->wherePivot('stato', '1')->first();
  }

  public function posizioniStorico(){
    return $this->posizioni()
                ->wherePivot('stato', '0');
                //->withPivot('posizione_famiglia','data_entrata', "data_uscita");
  }

  public function modificaDataInizioPosizione($posizione_id, $currentDatain, $newDataIn){
    $res = DB::connection('db_nomadelfia')->update( 
      DB::raw("UPDATE persone_posizioni
               SET  data_inizio = :new
               WHERE posizione_id  = :posizone AND persona_id = :persona AND data_inizio = :current"),
               array("posizone"=> $posizione_id, 'persona' => $this->id, "current"=>$currentDatain,  "new"=>$newDataIn)
      );
    return $res;
  }

  public function concludiPosizione($posizione_id, $datain, $datafine){
    $res = DB::connection('db_nomadelfia')->update( 
      DB::raw("UPDATE persone_posizioni
               SET stato = '0', data_fine = :dataout
               WHERE posizione_id  = :posizone AND persona_id = :persona AND data_inizio = :datain"),
               array( "posizone"=> $posizione_id, 'persona' => $this->id,"datain"=>$datain, "dataout"=>$datafine)
      );
    return $res;
  }

 
   /**
   * Ritorna le posizioni assegnabili ad una persona.
   * @return Collection Posizione 
   * @author Davide Neri
   **/
  public function posizioniPossibili(){
    $pos = self::posizioneAttuale();
    $posizioni = Posizione::all();
    if($pos != null){
      $posizioni = $posizioni->except([$pos->id]);
      if($pos->is(Posizione::find("EFFE")))
        return $posizioni->except([Posizione::find("FIGL")->id]);
      if($pos->is(Posizione::find("POST")))
        return $posizioni->except([Posizione::find("FIGL")->id]);
      if($pos->is(Posizione::find("OSPP")))
        return $posizioni->except([Posizione::find("EFFE")->id]);
      if($pos->is(Posizione::find("FIGL")))
        return $posizioni->except([Posizione::find("EFFE")->id]);
      return $posizioni;
    }else
    return $posizioni;
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
  public function cambiaGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale, $gruppoFamiliareNuovo, $dataEntrataGruppo){
    if($this->isCapoFamiglia() or $this->isSingle()){
        $this->famigliaAttuale()->assegnaFamigliaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale, $gruppoFamiliareNuovo, $dataEntrataGruppo);
    }
  }

  public function assegnaPersonaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale=null, $gruppoFamiliareNuovo, $dataEntrataGruppo=null){
    try
    {
      $this->gruppifamiliari()->updateExistingPivot($gruppoFamiliareAttuale,['stato' => '0','data_uscita_gruppo'=>$dataUscitaGruppoFamiliareAttuale]);
      $this->gruppifamiliari()->attach($gruppoFamiliareNuovo, ['stato' => '1','data_entrata_gruppo'=>$dataEntrataGruppo]);
    }catch (\Exception $e)
    {
     throw $e;
    }
  }

}
