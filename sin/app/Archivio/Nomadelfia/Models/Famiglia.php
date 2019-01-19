<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Traits\Enums;


class Famiglia extends Model
{
  use Enums;

  protected $connection = 'db_nomadelfia';
  protected $table = 'famiglie';
  protected $primaryKey = "id";

  protected $guarded = [];

  protected $enumPosizione = [  
    'CAPO FAMIGLIA',
    'MOGLIE',
    'FIGLIO NATO',
    'FIGLIO ACCOLTO',
    'SINGLE'
   ];

  public function scopeFamigliePerPosizioni($query, $posizione, $stato=1){
     return  $query->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'famiglie.id')
             ->join('persone', 'famiglie_persone.persona_id', '=', 'persone.id')
              ->select('famiglie.*',"persone.sesso", 'famiglie_persone.posizione_famiglia','famiglie_persone.stato' )
              ->where("posizione_famiglia", $posizione)
              ->where("stato", $stato);
  }

  /**
  * Ritorna le famiglie che hanno come capo famiglia un maschio
  * @author Davide Neri
  **/
  public function scopeMaschio($query){
    return $query->where("sesso","M");
  }

  /**
  * Ritorna le famiglie che hanno come capo famiglia una femmina
  * @author Davide Neri
  **/
  public function scopeFemmina($query){
    return $query->where("sesso","F");
  }

  /**
  * Ritorna tutti capi famiglie delle famiglie
  * @author Davide Neri
  **/
  public static function OnlyCapofamiglia(){
    return self::FamigliePerPosizioni("CAPO FAMIGLIA", '1');
  }

  /**
  * Ritorna tutti i signle  delle famiglie
  * @author Davide Neri
  **/
  public static function OnlySingle(){
    return self::FamigliePerPosizioni("SINGLE", '1');
  }
  
 /**
  * Ritorna tutti i gruppi familiari i cui ha vissuto la famiglia.
  * @author Davide Neri
  **/
  public function gruppiFamiliari()
  {
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_famiglie','famiglia_id','gruppo_famigliare_id')
                ->withPivot('data_inizio','data_fine','stato');
  }

 /**
  * Ritorna il gruppi familiari attuale in cui vide la famiglia
  * @author Davide Neri
  **/
  public function gruppoFamiliareAttuale()
  {
    return $this->gruppiFamiliari()->wherePivot('stato','1')->first();
  }

  /**
  * Ritorna i gruppi familiari storici in cui ha vissuto la famiglia
  * @author Davide Neri
  **/
  public function gruppiFamiliariStorico()
  {
    return $this->gruppiFamiliari()->wherePivot('stato','0');
  }


  /**
  * Ritorna i componenti che hanno fatto parte della famiglia (padre, madre, e figli)
  * @author Davide Neri
  **/
  public function componenti(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
            ->withPivot("stato",'posizione_famiglia')
            ->orderby("nominativo");
  }

  /**
  * Ritorna i componenti attuali della famiglia (padre, madre, e figli)
  * @author Davide Neri
  **/
  public function componentiAttuali()
  {
    return $this->componenti()->where("stato",'1');
  }

  /**
  * Ritorna il capofamiglia della famiglia.
  * @author Davide Neri
  **/
  public function capofamiglia(){
    return $this->componenti()
                ->wherePivot('posizione_famiglia','CAPO FAMIGLIA')
                ->first();
  }

  /**
  * Ritorna la persona single della famiglia.
  * @author Davide Neri
  **/
  public function single(){
    return $this->componenti()
                ->wherePivot('posizione_famiglia','SINGLE')
                ->first();
  }

  /**
  * Ritorna la persona moglie della famiglia.
  * @author Davide Neri
  **/
  public function moglie(){
    return $this->componenti()
                ->wherePivot('posizione_famiglia','MOGLIE')
                ->first();
  }

   /**
  * Ritorna i figli attuali (sia nati che accolti) della famiglia.
  * @author Davide Neri
  **/
  public function figli(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->withPivot("stato",'posizione_famiglia')
                ->wherePivotIn('posizione_famiglia',['FIGLIO NATO','FIGLIO ACCOLTO'])
                ->orderBy('data_nascita');
  }

  /**
  * Ritorna i figli attuali (sia nati che accolti) della famiglia.
  * @author Davide Neri
  **/
  public function figliAttuali(){
    return $this->figli()->wherePivot('stato',"=",'1');
  }

  /**
  * Assegna un nuovo gruppo familiare alla famiglia.
  * Se il gruppoFamiliare attuale non è nullo aggiorna lo stato =0.
  * @author Davide Neri
  **/
  public function assegnaFamigliaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale=null, 
                                                      $gruppoFamiliareNuovo, $dataEntrataGruppo=null)
  {
    try
    { if($gruppoFamiliareAttuale)
         $this->gruppiFamiliari()->updateExistingPivot($gruppoFamiliareAttuale,['stato' => '0','data_fine'=>$dataUscitaGruppoFamiliareAttuale]);
      
      $this->gruppiFamiliari()->attach($gruppoFamiliareNuovo,['stato' => '1','data_inizio'=>$dataUscitaGruppoFamiliareAttuale, 'data_fine'=>$dataEntrataGruppo]);
      foreach($this->componentiAttuali as $persona)
        $persona->assegnaPersonaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale, $gruppoFamiliareNuovo, $dataEntrataGruppo);
      
      //  $this->commit();
    }catch (Exception $e)
      {
       DB::rollBack();
       throw $e;
      }
      
  }


}


