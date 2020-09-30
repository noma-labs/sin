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

    /**
   * Set the nome in uppercase when a new famiglia is insereted.
   */
  public function setNomeFamigliaAttribute($value) {
    $this->attributes['nome_famiglia'] = strtoupper($value);
}

  public function scopeFamigliePerPosizioni($query, $posizione, $stato=1){
     return  $query->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'famiglie.id')
             ->join('persone', 'famiglie_persone.persona_id', '=', 'persone.id')
              ->select('famiglie.*',"persone.sesso", 'famiglie_persone.posizione_famiglia','famiglie_persone.stato' )
              ->where("posizione_famiglia", $posizione)
              ->where("famiglie_persone.stato", $stato);
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
  * Ritorna il gruppi familiare attuale in cui vive il CAPO FAMIGLIA e il SINGLE della famiglia.
  * Si ingeferise che tutta la famiglia vive nello stesso gruppo del CAPO FAMIGLIA o SINGLE:
  * @author Davide Neri
  **/

  public function gruppoFamiliareAttuale()
  {
    $res = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT gruppi_familiari.*, gruppi_persone.data_entrata_gruppo
      FROM famiglie_persone
      INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
      INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
      WHERE (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA' or famiglie_persone.posizione_famiglia = 'SINGLE') and famiglie_persone.famiglia_id = :famiglia_id and gruppi_persone.stato = '1'"),
      array("famiglia_id"=> $this->id)
    );  
   return $res;

  }

  /**
  * Ritorna i gruppi familiari storici in cui ha vissuto il CAPO FAMIGLIA o il SINGLE della famiglia
  * @author Davide Neri
  **/
  public function gruppiFamiliariStorico()
  {
     $res = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT gruppi_familiari.*, gruppi_persone.data_entrata_gruppo, gruppi_persone.data_uscita_gruppo
      FROM famiglie_persone
      INNER JOIN gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
      INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
      WHERE (famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA' or famiglie_persone.posizione_famiglia = 'SINGLE') and famiglie_persone.famiglia_id = :famiglia_id and gruppi_persone.stato = '0'"),
      array("famiglia_id"=> $this->id)
    );  
   return $res;
  }


  /**
  * Ritorna i componenti che hanno fatto parte della famiglia (padre, madre, e figli)
  * @author Davide Neri
  **/
  public function componenti(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
            ->withPivot("stato",'posizione_famiglia', 'data_entrata','data_uscita')
            ->orderby("nominativo");
  }

  /**
  * Ritorna i componenti attuali della famiglia (padre, madre, e figli)
  * @author Davide Neri
  **/
  public function componentiAttuali()
  {
    return $this->componenti()->where("famiglie_persone.stato",'1');
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
                ->withPivot("stato",'posizione_famiglia','data_entrata','data_uscita')
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
  * Rimuove tutti i componento della famiglia da un gruppo familiare
  * @author Davide Neri
  **/
  public function rimuoviDaGruppoFamiliare($idGruppo){

    DB::connection('db_nomadelfia')->update(
      DB::raw("UPDATE gruppi_persone
              SET
                  gruppi_persone.stato = '0'
              WHERE
                gruppi_persone.gruppo_famigliare_id = :gruppoattuale
                AND gruppi_persone.persona_id IN (
                      SELECT persone.id
                      FROM famiglie_persone
                      INNER JOIN persone ON persone.id = famiglie_persone.persona_id
                      #INNER join gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
                      WHERE famiglie_persone.famiglia_id = :famigliaId  AND famiglie_persone.stato = '1' #AND gruppi_persone.stato = '1'
                )
                AND gruppi_persone.stato = '1' "), 
              array('gruppoattuale' => $idGruppo, 'famigliaId'=> $this->id)
    );

  }


  /**
  * Assegna un nuovo gruppo familiare alla famiglia.
  * @author Davide Neri
  **/
  public function assegnaFamigliaANuovoGruppoFamiliare($gruppo_attuale_id, $dataUscitaGruppoFamiliareAttuale=null, $gruppo_nuovo_id, $dataEntrataGruppo=null)
  { 
    $famiglia_id = $this->id;
    $data_entrata = $dataEntrataGruppo;
    DB::transaction(function () use(&$gruppo_attuale_id, &$famiglia_id, &$gruppo_nuovo_id, &$data_entrata) {
     
      // Disabilita tutti i componento della famiglia nelvechi gruppo (metti stato = 0)
     DB::connection('db_nomadelfia')->update(
        DB::raw("UPDATE gruppi_persone
                SET
                    gruppi_persone.stato = '0'
                WHERE
                  gruppi_persone.gruppo_famigliare_id = :gruppoattuale
                  AND gruppi_persone.persona_id IN (
                        SELECT persone.id
                        FROM famiglie_persone
                        INNER JOIN persone ON persone.id = famiglie_persone.persona_id
                        #INNER join gruppi_persone ON gruppi_persone.persona_id = famiglie_persone.persona_id
                        WHERE famiglie_persone.famiglia_id = :famigliaId  AND famiglie_persone.stato = '1' #AND gruppi_persone.stato = '1'
                  )
                      
                  AND gruppi_persone.stato = '1' "), 
                array('gruppoattuale' => $gruppo_attuale_id, 'famigliaId'=> $famiglia_id)# , 'data_uscita'=>$dataUscitaGruppoFamiliareAttuale)
      );
      
      // Aggiungi a tutti i componenti della famiglia nel nuovo gruppo
      DB::connection('db_nomadelfia')->update(
        DB::raw("INSERT INTO gruppi_persone (persona_id, gruppo_famigliare_id, stato, data_entrata_gruppo)
                SELECT persone.id, :gruppo_nuovo_id, '1', :data_entrata
                FROM famiglie_persone
                INNER JOIN persone ON persone.id = famiglie_persone.persona_id
                WHERE famiglie_persone.famiglia_id = :famigliaId   AND famiglie_persone.stato = '1' "), 
                array( 'famigliaId'=> $famiglia_id, 'gruppo_nuovo_id' => $gruppo_nuovo_id, 'data_entrata'=> $data_entrata)# , 'data_uscita'=>$dataUscitaGruppoFamiliareAttuale)
      );
  });

  }
}


