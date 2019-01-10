<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\GruppoFamiliare;


class Famiglia extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'famiglie';
  protected $primaryKey = "id";

  protected $guarded = [];

  public function componenti(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->withPivot("posizione_famiglia");
  }

  public function componentiAttuali()
  {
    return $this->componenti()->where("stato",'1');
  }

  public function capofamiglia(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->wherePivot('posizione_famiglia','CAPO FAMIGLIA');
  }

  public function capifamiglia() {
      return $this->capofamiglia()->wherePivot('posizione_famiglia', "CAPO FAMIGLIA");
  }

  public function moglie(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->wherePivot('posizione_famiglia','MOGLIE')
                ->first();
  }

  public function figliAttuali(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->wherePivotIn('posizione_famiglia',['FIGLIO NATO','FIGLIO ACCOLTO'])
                ->wherePivot('stato',"=",'1')
                ->orderBy('data_nascita');
  }

  public function single(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->wherePivot('posizione_famiglia','SINGLE');
  }

  public  function scopeByNucleoFamigliare($quey){
    // return $this->whereHas('componenti',function($query) use($nucleo){
    //   $query->where('nucleo_famigliare_id',$nucleo->id);
    // });
  }

  public function gruppiFamiliari()
  {
    return $this->belongsToMany(GruppoFamiliare::class,'gruppi_famiglie','famiglia_id','gruppo_famigliare_id');
  }

  public function gruppoFamiliareAttuale()
  {
    return $this->gruppiFamiliari()->wherePivot('stato','1')->first();
  }

  public function gruppiFamiliariStorico()
  {
    return $this->gruppiFamiliari()->wherePivot('stato','0');
  }

  public function assegnaFamigliaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale=null, $gruppoFamiliareNuovo, $dataEntrataGruppo=null)
  {
    $this->gruppiFamiliari()->updateExistingPivot($gruppoFamiliareAttuale,['stato' => '0','data_fine'=>$dataUscitaGruppoFamiliareAttuale]);
    $this->gruppiFamiliari()->attach($gruppoFamiliareNuovo,['stato' => '1','data_inizio'=>$dataUscitaGruppoFamiliareAttuale, 'data_fine'=>$dataEntrataGruppo]);
    foreach($this->componentiAttuali as $persona)
       $persona->assegnaPersonaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale, $gruppoFamiliareNuovo, $dataEntrataGruppo);
  }


}


