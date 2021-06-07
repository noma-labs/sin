<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Excpetions\StatoDoesNotExists;
use Illuminate\Support\Facades\DB;
use \stdClass;
use Carbon;

/*
*
*  Static methods for obtaning statistics on the popolazione di Nomadelfia
*/
class PopolazioneNomadelfia
{

  /*
  *  Ritorna le persone della popolazione di Nomadelfia
  *
  */
    public static function popolazione()
    {
        $res = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT popolazione.*, posizioni.nome as posizione
                FROM (
                    SELECT persone.*, persone_categorie.data_inizio as data_entrata
                    FROM persone
                    INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
                    WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND persone_categorie.stato = '1'
                ) as popolazione
                LEFT JOIN persone_posizioni ON persone_posizioni.persona_id = popolazione.id
                LEFT JOIN posizioni ON posizioni.id = persone_posizioni.posizione_id
                WHERE  ( persone_posizioni.stato = '1' OR persone_posizioni.stato IS NULL)
                ORDER BY nominativo"
            )
        );
        return $res;
    }

  

    /*
    *  Ritorna il totale della popolazione attuale
    *  Una persona fa parte della popolazione se e solo se
    *       - è una persona attiva (stato = '1)
    *       - è una persona con categoria "Persona interna"
    */
    public static function totalePopolazione()
    {
        $interna = Categoria::perNome("interno");
        $res = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT count(*) as popolazione
                FROM persone
                INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
                WHERE persone_categorie.categoria_id = :interna AND persone.stato = '1' AND persone_categorie.stato = '1'"
            ),
            array('interna'=> $interna->id)
        );
        return $res[0]->popolazione;
    }

    /*
     *  Ritorna tutte e persone maggiorenni della popoalzione
     */
    public static function maggiorenni($orderby='data_nascita')
    {
        $interna = Categoria::perNome("interno");
        $magg =  DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT persone.*, persone_categorie.* 
              FROM persone
              INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
              WHERE persone_categorie.categoria_id = :interna AND persone.stato = '1' AND persone_categorie.stato = '1'
                AND persone.data_nascita <= DATE_SUB(NOW(), INTERVAL 18 YEAR)
                  ORDER BY :order"),
            array('interna'=> $interna->id, 'order'=>$orderby)
        );
        $result = new stdClass;
        $maggioreni = collect($magg);
      
        $sesso = $maggioreni->groupBy("sesso");
        $result->total =  $maggioreni->count();
        $result->uomini =  $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
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
        $result->uomini = $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
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
        $result->uomini =  $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
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
        $result->uomini =  $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
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
  *  Ritorna i figli maggiorenni
  */
    public static function figliMaggiorenni($orderby='data_nascita')
    {
        $magg = self::figliDaEta(18, null, $orderby);
        $result = new stdClass;
        $maggioreni = collect($magg);
    
        $sesso = $maggioreni->groupBy("sesso");
        $result->total =  $maggioreni->count();
        $result->uomini =  $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }

  
    /*
    *  Ritorna i minorenni
    */
    public static function figliMinorenni()
    {
        $magg = self::figliDaEta(0, 18);
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy("sesso");
        $result->total =  $maggioreni->count();
        $result->uomini =  $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }

    /*
    *  Ritorna i minorenni divisi per anno di eta.
    */
    public static function figliMinorenniPerAnno()
    {
        $minorenni = collect(self::figliDaEta(0, 18));
        $result = new stdClass;
        $result->total =  $minorenni->count();
        $minorenni->map(function ($item, $key) {
            return $item->anno = Carbon::parse($item->data_nascita)->year;
        });
        $groupMinorenni= $minorenni->sortBy(function ($persona, $key) {
            return $persona->anno;
        });
        $result->anno = $groupMinorenni->groupby(['anno', function ($item) {
            return $item->sesso;
        }]);
        return $result;
    }

    /*
     *  Ritorna i figlio fra 18 e 21 anni
     */
    public static function figliFra18e21()
    {
        $magg = self::figliDaEta(18, 21);
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy("sesso");
        $result->total =  $maggioreni->count();
        $result->uomini =  $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }

    public static function figliMaggiori21()
    {
        $magg = self::figliDaEta(21, null, "nominativo");
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy("sesso");
        $result->total =  $maggioreni->count();
        $result->uomini =  $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }

    /*
    *  Ritorna i sacerdoti della popolazione
    */
    public static function sacerdoti()
    {
        return self::byStati("SAC");
    }


    /*
    *  Ritorna le mamme di vocazione  della popolazione
    */
    public static function mammeVocazione()
    {
        return self::byStati("MAV");
    }

    /*
  *  Ritorna le nomadelfa mamma  della popolazione
  */
    public static function nomadelfaMamma()
    {
        return self::byStati("MAM");
    }



    /*
    *  Ritorna le persone con una certa posizione.
    *  LA posizione è una tra "DADE", "EFFE", "FIGL", "OSPP" "POST"
    */
    public static function byPosizione(string $posizione)
    {
        $posizioni = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT persone.*, persone_posizioni.*
      FROM persone
      INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
      INNER JOIN posizioni ON posizioni.id = persone_posizioni.posizione_id
      WHERE persone.stato = '1' AND persone_posizioni.stato = '1' and posizioni.abbreviato = :posizione AND persone.deleted_at is  NULL
      ORDER by persone_posizioni.data_inizio ASC, persone.nominativo"
            ),
            array("posizione"=>$posizione)
        );
        return $posizioni;
    }

    /*
    *  Ritorna il numero per persone attive per ogni categoria
    */
    public static function perCategorie()
    {
        $posizioni = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT categorie.nome, count(*) as count
                FROM persone
                INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
                INNER JOIN categorie ON categorie.id = persone_categorie.categoria_id
                WHERE persone.stato = '1' AND persone_categorie.stato = '1'
                GROUP BY categorie.nome"
            )
        );
        return $posizioni;
    }

    /*
  *  Ritorna le persone con una certo stato.
  *  Lo stato è una tra "CDE", "CEL", "MAM", "NUB" "MAN", "SAC", "SPO", "VEDE
  */
    public static function byStati(string $stato)
    {
        $posizioni = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT persone.*, persone_stati.*
                FROM persone
                INNER JOIN persone_stati ON persone_stati.persona_id = persone.id
                INNER JOIN stati ON stati.id = persone_stati.stato_id
                WHERE persone.stato = '1' AND persone_stati.stato = '1' and stati.stato = :stato AND persone.deleted_at is  NULL
                ORDER by persone_stati.data_inizio ASC, persone.nominativo"
            ),
            array("stato"=>$stato)
        );
        return $posizioni;
    }
    /*
    *  Ritorna il numero per persone attive per ogni posizione (postulante, effettivo, ospite, figlio)
    */
    public static function perPosizioni()
    {
        $posizioni = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT posizioni.nome, count(*) as count
                FROM persone
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                INNER JOIN posizioni ON posizioni.id = persone_posizioni.posizione_id
                WHERE persone.stato = '1' AND persone_posizioni.stato = '1'  AND persone.deleted_at is NULL
                group by posizioni.nome
                ORDER BY posizioni.ordinamento"
            )
        );
        return $posizioni;
    }

    /*
    *  Ritorna il numero di persone per ogni gruppo familiare

    public static function gruppiComponenti()
    {
      $gruppi = DB::connection('db_nomadelfia')->select(
        DB::raw("SELECT gruppi_familiari.id,  count(*) as componenti
        FROM gruppi_persone
        INNER JOIN persone ON gruppi_persone.persona_id = persone.id
        INNER JOIN gruppi_familiari ON gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
        WHERE persone.stato = '1' and gruppi_persone.stato = '1'
        GROUP by gruppi_familiari.id
        ORDER BY gruppi_familiari.nome"
       ));
      return $gruppi;
    }
     */


 

    /*
    *  Ritorna i figli con hanno gli anni maggiori di $frometa (e minori di $toEta se non nullo)
    */
    public static function figliDaEta(int $fromEta, int $toEta=null, string $orderBy='data_nascita')
    {
        if ($toEta == null) {
            return DB::connection('db_nomadelfia')->select(
                DB::raw("SELECT persone.*, persone_posizioni.* 
                FROM persone
                INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                INNER JOIN posizioni ON persone_posizioni.posizione_id = posizioni.id
                WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND persone_categorie.stato = '1' AND persone_posizioni.stato = '1'
                    AND persone.data_nascita <= DATE_SUB(NOW(), INTERVAL :anni YEAR)
                    AND posizioni.abbreviato = 'FIGL'
                    ORDER BY :order"),
                array('anni'=>$fromEta, 'order'=>$orderBy)
            );
        } else {
            return  DB::connection('db_nomadelfia')->select(
                DB::raw("SELECT persone.*, persone_posizioni.*
                FROM persone
                INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                INNER JOIN posizioni ON persone_posizioni.posizione_id = posizioni.id
                WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND persone_categorie.stato = '1' AND persone_posizioni.stato = '1'
                    AND persone.data_nascita <= DATE_SUB(NOW(), INTERVAL :fromanni YEAR)  AND  persone.data_nascita > DATE_SUB(NOW(), INTERVAL :toanni YEAR)
                    AND  posizioni.abbreviato = 'FIGL'
                    ORDER BY  :order"),
                array("fromanni" => $fromEta, "toanni" =>$toEta, 'order'=>$orderBy)
            );
        }
    }

    /*
    *  Ritorna il numero di componente per ogni gruppo
    */
    /*
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
    } */

    /*
    *  Ritorna la famiglie e i componenti nel nucleo familiare che vivono attualmente in Nomadelfia.
    *
    */
    public static function famiglie()
    {
        $interna = Categoria::perNome("interno");

        $famiglie = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita 
                FROM persone 
                INNER JOIN famiglie_persone ON famiglie_persone.persona_id = persone.id 
                INNER JOIN persone_categorie on persone_categorie.persona_id = famiglie_persone.persona_id
                LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id 
                WHERE persone_categorie.categoria_id = :interna 
                    AND persone_categorie.stato = '1'
                    AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL)
                    AND (famiglie_persone.posizione_famiglia != 'SINGLE' OR famiglie_persone.stato IS NULL)
                    AND persone.stato = '1'
                ORDER BY famiglie.nome_famiglia ASC, persone.data_nascita ASC"
            ),
            array('interna' => $interna->id)
        );
        $famiglie = collect($famiglie)->groupBy('famiglia_id');
        return $famiglie;
    }


    /*
    *  Ritorna il numero di persone per ogni posizione nella fmaiglia (maschi e femmine)
    */
    public static function posizioneFamigliaCount()
    {
        $gruppi = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT famiglie_persone.posizione_famiglia, persone.sesso, count(*) as count
              FROM famiglie_persone
              INNER JOIN persone ON famiglie_persone.persona_id = persone.id
              INNER JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id
              WHERE persone.stato = '1'  AND famiglie_persone.stato = '1'
              GROUP BY famiglie_persone.posizione_famiglia,  persone.sesso"
            )
        );
        return $gruppi;
    }
}
