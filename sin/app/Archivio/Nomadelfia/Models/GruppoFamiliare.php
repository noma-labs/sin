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

  protected $guarded = [''];

  
  public function capogruppi()
  {
    return $this->belongsToMany(Persona::class,'gruppi_familiari_capogruppi','gruppo_familiare_id','persona_id');
  }

  public function capogruppoAttuale()
  {
    return $this->belongsToMany(Persona::class,'gruppi_familiari_capogruppi','gruppo_familiare_id','persona_id')
                ->wherePivot('stato', 1)
                ->first();
  }


  // deprecato: usare il metodo componenti()
  public function persone()
  {
    return $this->belongsToMany(Persona::class,'gruppi_persone','gruppo_famigliare_id','persona_id')
                ->withPivot("stato")
                ->orderBy("data_nascita", 'ASC');
  }

  public function personeAttuale(){
    return $this->persone()->wherePivot("stato","1");
  }

  /*
  * Ritorna il numero di componenti per un singolo gruppo familiare
  * Le persone sono contate nel gruppo se:
  *       - è una persona attiva
  *       - è una persone con categoria diversa da "persona esterna"
  */
  public function componenti()
  {
    $gruppi = DB::connection('db_nomadelfia')->select( 
      DB::raw("
      SELECT  persone.*
      FROM persone
      INNER JOIN gruppi_persone ON gruppi_persone.persona_id = persone.id
      INNER JOIN persone_categorie ON persone_categorie.persona_id = gruppi_persone.persona_id
      WHERE gruppi_persone.stato = '1' AND persone_categorie.categoria_id != 4 AND gruppi_persone.stato = '1'
           AND gruppi_persone.gruppo_famigliare_id = :gruppo
      order by persone.data_nascita ASC"), array('gruppo' => $this->id));
    return $gruppi;
  }

  /*
  * Ritorna il numero di componenti per ogni gruppi familiare
  * Le persone sono contate nel gruppo se:
  *       - è una persona attiva
  *       - è una persone con categoria diversa da "persona esterna"
  */
  public static function countComponenti()
  {
    $gruppi = DB::connection('db_nomadelfia')->select( 
      DB::raw("
          SELECT  gruppi_familiari.id, max(gruppi_familiari.nome) as nome,count(*) as count
          FROM gruppi_familiari
          INNER JOIN gruppi_persone ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
          INNER JOIN persone ON gruppi_persone.persona_id = persone.id
          INNER JOIN persone_categorie ON persone_categorie.persona_id = gruppi_persone.persona_id
          WHERE gruppi_persone.stato = '1' AND persone_categorie.categoria_id != 4 AND gruppi_persone.stato = '1'
          GROUP BY gruppi_familiari.id
          order by gruppi_familiari.nome"));
    return $gruppi;
  }

  /*
  * Ricostrutisce le famiglie del gruppo familiare partendo dalle persone presenti.
  * 
  */
  public function Famiglie()
  {
    $famiglie = DB::connection('db_nomadelfia')->select( 
      DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita 
      FROM gruppi_persone 
      LEFT JOIN famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id 
      INNER JOIN persone ON gruppi_persone.persona_id = persone.id 
      LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id 
      WHERE gruppi_persone.gruppo_famigliare_id = :gruppo 
          AND gruppi_persone.stato = '1' 
          AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL)
          AND (famiglie_persone.posizione_famiglia != 'SINGLE' OR famiglie_persone.stato IS NULL)
          AND persone.stato = '1'
      ORDER BY  persone.data_nascita ASC"), array('gruppo' => $this->id));
    $famiglie = collect($famiglie)->groupBy('famiglia_id');
    return $famiglie;
  }


  /*
  * Ritorna famiglie SINGLE del gruppo familiare partendo dalle persone presenti.
  * Il controllo nella query (famiglie_persone.stato IS NULL) viene usato per selezionare anche le persone senza una famiglia.
  *  
  */
  public function Single()
  {
    $single = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita 
              FROM gruppi_persone 
              LEFT JOIN famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id 
              INNER JOIN persone ON gruppi_persone.persona_id = persone.id 
              LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id 
              WHERE gruppi_persone.gruppo_famigliare_id = :gruppo
                  AND gruppi_persone.stato = '1' 
                  AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL) 
                  AND (famiglie_persone.posizione_famiglia = 'SINGLE' OR famiglie_persone.stato IS NULL
                  AND persone.stato = '1')
              ORDER BY persone.sesso DESC, persone.data_nascita  ASC"), 
              array('gruppo' => $this->id)
    );
    return $single;
  }




  /**
   * Ritorna il numero di persone con una certa 
   * posizione familiare  (capofamiglia, moglie, figlio nato, accolto,...) 
   * che vivono in un gruppo familiare.         
   * 
   *  @author Davide Neri                                               
   */
  
  public function scopeCountPosizioniFamiglia($query, $gruppoId){
    return  $query->join('gruppi_famiglie', 'gruppi_famiglie.gruppo_famigliare_id', '=', 'gruppi_familiari.id')
                  ->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'gruppi_famiglie.famiglia_id')
                  ->select('gruppi_famiglie.gruppo_famigliare_id', 'famiglie_persone.posizione_famiglia',  DB::raw('count(2) as total'))
                  ->where("gruppi_famiglie.stato",'1')
                  ->where("gruppi_familiari.id", $gruppoId)
                  ->where("famiglie_persone.stato",'1')
                  ->groupBy("gruppi_famiglie.gruppo_famigliare_id","famiglie_persone.posizione_famiglia");
  }
  

  public function scopePersoneConFamiglia($query, $gruppoid){
      return self::find($gruppoid)->personeAttuale()->with(["famiglie"=>function($query){$query->where("stato","1"); }]);
  }

}
