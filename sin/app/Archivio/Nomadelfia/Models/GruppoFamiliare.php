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
                ->orderBy("data_nascita", 'ASC');
  }

  public function personeAttuale(){
    return $this->persone()->wherePivot("stato","1");
  }

  public function famiglie()
  {
    return $this->belongsToMany(Famiglia::class,'gruppi_famiglie','gruppo_famigliare_id','famiglia_id')
                ->orderby("nome_famiglia");
  }

  public function famiglieAttuale()
  {
    return $this->belongsToMany(Famiglia::class,'gruppi_famiglie','gruppo_famigliare_id','famiglia_id')
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
   * Ritorna il numero di posizioni_famiglia (capofamiglia, moglie, figlio nato, accolto,...) 
   * in un gruppo familiare.                                                       \\2
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

  // /**
  //  * Ritorna il numero di persone maschi e femmina in un gruppo familiare.                                                       \\2
  //  */
  // public function scopeCountPosizioniFamiglia($query, $gruppoId){
  //   return  $query->join('gruppi_famiglie', 'gruppi_famiglie.gruppo_famigliare_id', '=', 'gruppi_familiari.id')
  //                 ->join('famiglie_persone', 'famiglie_persone.famiglia_id', '=', 'gruppi_famiglie.famiglia_id')
  //                 ->select('gruppi_famiglie.gruppo_famigliare_id', 'famiglie_persone.posizione_famiglia',  DB::raw('count(2) as total'))
  //                 ->where("gruppi_famiglie.stato",'1')
  //                 ->where("gruppi_familiari.id", $gruppoId)
  //                 ->where("famiglie_persone.stato",'1')
  //                 ->groupBy("gruppi_famiglie.gruppo_famigliare_id","famiglie_persone.posizione_famiglia");
  // }

}
