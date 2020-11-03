<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Excpetions\StatoDoesNotExists;
use Illuminate\Support\Facades\DB;
use \stdClass;


/*  
* 
*  Static methods for obtaning statistics on the popolazione di Nomadelfia
*/
class PopolazioneNomadelfia
{

  /*
  *  Ritorna il totale della popolazione attuale
  *  Una persona fa parte della popolazione se e solo se
  *       - è una persona attiva (stato = '1)
  *       - è una persona con categoria "Persona interna"
  */
  public static function totalePopolazione(){
    $res = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT count(*) as popolazione
            FROM persone
            INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
            WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND persone_categorie.stato = '1'" 
     ));
     return $res[0]->popolazione;
  }


  /*
  *  Ritorna i nomadelfi effettivi  della popolazione divisi per uomini e donne
  *
  */
  public static function effettivi()
  {
    $result = new stdClass;
    $effettivi = collect(self::byPosizione("EFFE"));
    $sesso = $effettivi->groupBy("sesso");
    $result->total =  $effettivi->count();
    $result->uomini =  $sesso->get("M");
    $result->donne = $sesso->get("F");
    return $result;

  }

  /*
  *  Ritorna i postulanti  della popolazione
  */
  public static function postulanti()
  {
    $result = new stdClass;
    $postulanti = collect(self::byPosizione("POST"));
    $sesso = $postulanti->groupBy("sesso");
    $result->total =  $postulanti->count();
    $result->uomini =  $sesso->get("M");
    $result->donne = $sesso->get("F");
    return $result;
  }

     /*
  *  Ritorna gli ospiti  della popolazione
  */
  public static function ospiti()
  {
    $result = new stdClass;
    $ospiti = collect(self::byPosizione("OSPP"));
    $sesso = $ospiti->groupBy("sesso");
    $result->total =  $ospiti->count();
    $result->uomini =  $sesso->get("M");
    $result->donne = $sesso->get("F");
    return $result;
  }

   /*
  *  Ritorna i figli  della popolazione
  */
  public static function figli()
  {
    return self::byPosizione("FIGL");
  }


  /*
  *  Ritorna le persone con una certa posizione.
  *  LA posizione è una tra "DADE", "EFFE", "FIGL", "OSPP" "POST"
  */
  public static function byPosizione(string $posizione)
  {
    $posizioni = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT persone.*, persone_posizioni.*
      FROM persone
      INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
      INNER JOIN posizioni ON posizioni.id = persone_posizioni.posizione_id
      WHERE persone.stato = '1' AND persone_posizioni.stato = '1' and posizioni.abbreviato = :posizione AND persone.deleted_at is  NULL
      ORDER by persone_posizioni.data_inizio ASC, persone.nominativo"
     ), array("posizione"=>$posizione));
    return $posizioni;
  }

  /*
  *  Ritorna il numero per persone attive per ogni categoria
  */
  public static function perCategorie()
  {
    $posizioni = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT categorie.nome, count(*) as count
                FROM persone
                INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
                INNER JOIN categorie ON categorie.id = persone_categorie.categoria_id
                WHERE persone.stato = '1' AND persone_categorie.stato = '1'
                GROUP BY categorie.nome"
     ));
    return $posizioni;
  }
  /*
  *  Ritorna il numero per persone attive per ogni posizione (postulante, effettivo, ospite, figlio)
  */
  public static function perPosizioni()
  {
    $posizioni = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT posizioni.nome, count(*) as count
                FROM persone
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                INNER JOIN posizioni ON posizioni.id = persone_posizioni.posizione_id
                WHERE persone.stato = '1' AND persone_posizioni.stato = '1'  AND persone.deleted_at is NULL
                group by posizioni.nome
                ORDER BY posizioni.ordinamento"
     ));
    return $posizioni;
  }

  /*
  *  Ritorna il numero di persone maggiorenni e minorenni divisi per eta per ogni gruppo
  */
  public static function gruppiComponenti()
  {
    $gruppi = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT gruppi_familiari.nome,  count(*) as componenti
      FROM gruppi_persone
      INNER JOIN persone ON gruppi_persone.persona_id = persone.id
      INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
      WHERE persone.stato = '1' and gruppi_persone.stato = '1'
      GROUP by gruppi_familiari.nome
      ORDER BY gruppi_familiari.nome"
     ));
    return $gruppi;
  }


   /*
  *  Ritorna i maggiorenni donna
  */
  public static function MaggiorenniDonne()
  {
    $magg = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT *
              FROM persone
              INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
              WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND persone_categorie.stato = '1'
                  AND persone.data_nascita <= DATE_SUB(NOW(), INTERVAL 18 YEAR)
                  AND persone.sesso = 'F'
                  ORDER BY nominativo
                  " 

        ));
    return $magg;
  }

  /*
  *  Ritorna i maggiorenni uomini
  */
  public static function MaggiorenniUomini()
  {
    $magg = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT *
              FROM persone
              INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
              WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND persone_categorie.stato = '1'
                  AND persone.data_nascita <= DATE_SUB(NOW(), INTERVAL 18 YEAR)
                  AND persone.sesso = 'M'
                  ORDER BY nominativo" 
        ));
    return $magg;
  }

   /*
  *  Ritorna il numero di componente per ogni gruppo
  */
  public static function Minorenni()
  {
    $magg = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT *, YEAR(persone.data_nascita) as anno
              FROM persone
              INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
              WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND persone_categorie.stato = '1'
                  AND persone.data_nascita > DATE_SUB(NOW(), INTERVAL 18 YEAR)
                  ORDER BY data_nascita"
        ));
    return $magg;
  }



  /*
  *  Ritorna il  numero di persone per ogni posizione nella fmaiglia (maschi e femmine)
  */
  public static function posizioneFamigliaCount()
  {
    $gruppi = DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT famiglie_persone.posizione_famiglia, persone.sesso, count(*) as count
              FROM famiglie_persone
              INNER JOIN persone ON famiglie_persone.persona_id = persone.id
              INNER JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id
              WHERE persone.stato = '1'  AND famiglie_persone.stato = '1'
              GROUP BY famiglie_persone.posizione_famiglia,  persone.sesso"
     ));
    return $gruppi;
  }


}
