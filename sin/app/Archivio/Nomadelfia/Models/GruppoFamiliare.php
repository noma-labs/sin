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

  public function famiglie()
  {
    return $this->belongsToMany(Famiglia::class,'gruppi_famiglie','gruppo_famigliare_id','famiglia_id')
                ->withPivot("stato")
                ->orderby("nome_famiglia");
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
   * Ritorna le persone in un gruppo familiare 
   * tramite i componenti attuali delle famiglie.          
   * @author Davide Neri                                           
   */
  public function personeAttualeViaFamiglie(){
    $famiglie = self::famiglieAttuale()->get();
    $persone = collect();
    $famiglie->each(function ($famiglia) use ($persone){
       $famiglia->componentiAttuali->each(function($persona) use($persone) {
        $persone->push($persona);
       });
    });
    return $persone;
  }
  
 // ritorna l'id delle persone che sono nelle famiglie ma non nelle persone
  public function compare(){
    $collection = self::personeAttualeViaFamiglie();    # collect([1, 2, 3, 4, 5]);
    $persone = self::personeAttuale()->get();           # collect [2, 4, 6, 8]);
    $diff = $collection->diffKeys($persone);            # [1, 3, 5]
    return $diff;
  }


  /**
   * Ritorna il id delle famiglie che sono capo famiglia in un gruupo
   *  @author Davide Neri                 
   *   Gruppi
   *      id 
   * 
   *   GruppiPersone
   *      gruppo_id
   *      persona_id
   * 
   *   FamigliePersone
   *     famiglia_id
   *     persona_id
   *     posizione_famiglia
   * 
   *   Persona
   *     id   
   * 
   *   Famiglia
   *    id                           
   */
  public function scopeCapoFamiglia($gruppoId){
    return  DB::table('gruppi_persone')
                  ->select("famiglie_persone.famiglia_id")
                  ->join('famiglie_persone', 'famiglie_persone.persona_id', '=', 'gruppi_persone.persona_id')
                  ->where("famiglie_persone.posizione_famiglia", "CAPO FAMIGLIA")
                  ->where("gruppi_persone.gruppo_famigliare_id", $gruppoId);
                  
            //SELECT famiglie_persone.famiglia_id
            //FROM gruppi_persone
            //INNER join famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id  
            //where famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA' and gruppi_persone.gruppo_famigliare_id = 8
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
