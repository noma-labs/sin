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


  public function persone()
  {
    return $this->belongsToMany(Persona::class,'gruppi_persone','gruppo_famigliare_id','persona_id')
                ->withPivot("stato")
                ->orderBy("data_nascita", 'ASC');
  }

  public function personeAttuale(){
    return $this->persone()->wherePivot("stato","1");
  }

  // DEPRECATED. usare il metodo byFamiglie
  public function famiglie()
  {
    return $this->belongsToMany(Famiglia::class,'gruppi_famiglie','gruppo_famigliare_id','famiglia_id')
                ->withPivot("stato")
                ->orderby("nome_famiglia");
  }

  /*
  * Ricostrutisc le famifle del gruppo familiare partendo dalle persone presenti.
  *
  */
  public function scopebyFamiglie($query)
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

  



  public function famiglie2()
  {
    return $this->belongsToMany(Famiglia::class,'gruppi_famiglie','gruppo_famigliare_id','famiglia_id')
                ->withPivot("stato")
                ->orderby("nome_famiglia");
  }

  public function famiglieAttuale()
  {
    return $this->famiglie()
                ->wherePivot("stato","1");
  }

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
