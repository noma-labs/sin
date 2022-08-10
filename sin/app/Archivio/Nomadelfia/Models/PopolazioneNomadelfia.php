<?php

namespace App\Nomadelfia\Models;

use App\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use \stdClass;
use Carbon;


class PopolazioneNomadelfia extends Model
{
    protected $connection = 'db_nomadelfia';
    protected $table = 'popolazione';

    public $timestamps = true;
    protected $guarded = [];


    /*
    *  Ritorna le persone della popolazione di Nomadelfia
    */
    public static function popolazione()
    {
        $res = DB::connection('db_nomadelfia')->select(
            DB::raw("SELECT popolazione.*, persone.*,  persone_posizioni.*, posizioni.nome as posizione
                FROM popolazione
                LEFT JOIN persone p on popolazione.persona_id = p.id
                LEFT JOIN persone ON persone.id = popolazione.persona_id
                LEFT JOIN persone_posizioni ON persone_posizioni.persona_id = popolazione.persona_id
                LEFT JOIN posizioni ON posizioni.id = persone_posizioni.posizione_id
                WHERE popolazione.data_uscita IS NULL AND  (persone_posizioni.stato = '1'  OR persone_posizioni.stato IS NULL)
                ORDER BY nominativo")
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
        $res = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT count(*) as popolazione
                FROM popolazione
                INNER JOIN persone p ON p.id = popolazione.persona_id
                WHERE popolazione.data_uscita IS NULL"
            )
        );
        return $res[0]->popolazione;
    }

    /*
     *  Ritorna tutte e persone maggiorenni della popolazione divisi per uomini e donne
     */
    public static function maggiorenni($orderby = 'nominativo', $order = "ASC")
    {
//        $magg = DB::connection('db_nomadelfia')
//            ->table('persone')
//            ->selectRaw("persone.*, popolazione.*")
//            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
//            ->whereNull("popolazione.data_uscita")
//            ->where("persone.data_nascita", "<=", Carbon::now()->subYears(18))
//            ->orderByRaw("persone." . strval($orderby) . " " . $order)
//            ->get();
        $magg = self::daEta(18, $orderby, $order);
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy("sesso");
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }

    public static function daEta(int $eta, string $orderBy = 'nominativo', string $order ="ASC")
    {
        $magg = DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw("persone.*, popolazione.*")
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->whereNull("popolazione.data_uscita")
            ->where("persone.data_nascita", "<=", Carbon::now()->subYears($eta))
            ->orderByRaw("persone." . strval($orderBy) . " ". $order)
            ->get();
        return $magg;
    }

    public static function fraEta(
        int $fromEta,
        int $toEta = null,
        string $orderBy = 'nominativo',
        int $travel_to_year = null,
        $withInYear = false,
        string $order ='ASC'
    )
    {
        $date = ($travel_to_year == null ? Carbon::now() : Carbon::now()->setYear($travel_to_year));
        $end = $date->copy()->subYears($fromEta);
        if ($withInYear) {
            $end = $end->endOfYear();
        }
        $start = $date->copy()->subYears($toEta);
        if ($withInYear) {
            $start = $start->endOfYear();
        }
        $magg = DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw('persone.*, popolazione.*')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
//            ->where(function ($query) use ($end, $start) {
//                $query->whereNull('popolazione.data_uscita')
//                    ->orWhere(function ($query) use ($start, $end){
//                        $query->where('popolazione.data_entrata', ">=", $start)
//                             ->where('popolazione.data_uscita', "<=", $end);
//                });
//            })
            ->where('persone.data_nascita', '<=', $end)
            ->orderByRaw('persone.' . strval($orderBy) . ' ' . $order);
        if ($toEta != null) {
            $magg->where('persone.data_nascita', '>=', $start);
        }
        return $magg;

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
        $result->total = $effettivi->count();
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
        $result->total = $postulanti->count();
        $result->uomini = $sesso->get("M", []);
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
        $result->total = $ospiti->count();
        $result->uomini = $sesso->get("M", []);
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
    public static function figliMaggiorenni($orderby = 'nominativo')
    {
        $magg = self::figliDaEta(18, null, $orderby);
        $result = new stdClass;
        $maggioreni = collect($magg);

        $sesso = $maggioreni->groupBy("sesso");
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }


    /*
    *  Ritorna i minorenni
    */
    public static function figliMinorenni()
    {
        $magg = self::figliDaEta(0, 18, 'nominativo');
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy("sesso");
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }

    /*
    *  Ritorna i minorenni divisi per anno di eta.
    */
    public static function figliMinorenniPerAnno()
    {
        $minorenni = collect(self::figliDaEta(0, 18, 'nominativo'));
        $result = new stdClass;
        $result->total = $minorenni->count();
        $minorenni->map(function ($item, $key) {
            return $item->anno = Carbon::parse($item->data_nascita)->year;
        });
        $groupMinorenni = $minorenni->sortBy(function ($persona, $key) {
            return $persona->anno;
        });
        $result->anno = $groupMinorenni->groupby([
            'anno',
            function ($item) {
                return $item->sesso;
            }
        ]);
        return $result;
    }

    /*
     *  Ritorna i figlio fra 18 e 21 anni
     */
    public static function figliFra18e21()
    {
        $magg = self::figliDaEta(18, 21, 'nominativo');
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy("sesso");
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get("M", []);
        $result->donne = $sesso->get("F", []);
        return $result;
    }

    public static function figliMaggiori21()
    {
        $magg = self::figliDaEta(21, null, "nominativo");
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy("sesso");
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get("M", []);
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
    public static function byPosizione(string $posizione, $ordeby="nominativo")
    {  // "persone_posizioni.data_inizio ASC, persone.nominativo"

        $posizioni = DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw("persone.*, persone_posizioni.*")
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->join('persone_posizioni', 'persone_posizioni.persona_id', '=', 'persone.id')
            ->join('posizioni', 'posizioni.id', '=', 'persone_posizioni.posizione_id')
            ->whereNull("popolazione.data_uscita")
            ->where("persone_posizioni.stato", "=", '1')
            ->where("posizioni.abbreviato", "=", $posizione)
            ->orderByRaw($ordeby)
            ->get();
        return $posizioni;
    }

    /*
  *  Ritorna le persone con una certo stato.
  *  Lo stato è una tra "CDE", "CEL", "MAM", "NUB" "MAN", "SAC", "SPO", "VEDE
  */
    public static function byStati(string $stato, $orderby="nominativo")
    {  // persone_stati.data_inizio ASC, persone.nominativo"
        $stati = DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw("persone.*, persone_stati.*")
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->join('persone_stati', 'persone_stati.persona_id', '=', 'persone.id')
            ->join('stati', 'stati.id', '=', 'persone_stati.stato_id')
            ->whereNull("popolazione.data_uscita")
            ->where("persone_stati.stato", "=", '1')
            ->where("stati.stato", "=", $stato)
            ->whereRaw("persone.deleted_at IS NULL")
            ->orderByRaw($orderby)
            ->get();
        return $stati;
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
                WHERE persone_posizioni.stato = '1'  AND persone.deleted_at is NULL
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
    public static function figliDaEta(
        int $fromEta,
        int $toEta = null,
        string $orderBy = 'nominativo',
        int $travel_to_year=null,
        $withInYear=false
    )
    {
        $date = ($travel_to_year==null ? Carbon::now(): Carbon::now()->setYear($travel_to_year));

        $posizione = Posizione::perNome("figlio");
        $end =$date->copy()->subYears($fromEta);
        if ($withInYear){
            $end = $end->endOfYear();
        }
        $start = $date->copy()->subYears($toEta);
        if ($withInYear){
            $start = $start->endOfYear();
        }

        $q = DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw("persone.*, persone_posizioni.*")
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->join('persone_posizioni', 'persone_posizioni.persona_id', '=', 'persone.id')
            ->join('posizioni', 'persone_posizioni.posizione_id', '=', 'posizioni.id')
            ->whereNull("popolazione.data_uscita")
            ->where("persone_posizioni.stato", "=", "1")
            ->where("persone.data_nascita", "<=",$end)
            ->where("posizioni.abbreviato", "=", $posizione->abbreviato)
            ->orderByRaw($orderBy);
        if ($toEta != null){
            $q->where("persone.data_nascita", ">=", $start);
        }
        return $q->get();
    }

    /*
    *  Ritorna la famiglie e i componenti nel nucleo familiare che vivono attualmente in Nomadelfia.
    *
    */
    public static function famiglie()
    {
        $famiglie = DB::connection('db_nomadelfia')->select(
            DB::raw(
                "SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita 
                FROM persone 
                INNER JOIN famiglie_persone ON famiglie_persone.persona_id = persone.id 
                INNER JOIN popolazione on popolazione.persona_id = famiglie_persone.persona_id
                LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id 
                WHERE popolazione.data_uscita IS NULL
                    AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL)
                    AND (famiglie_persone.posizione_famiglia != 'SINGLE' OR famiglie_persone.stato IS NULL)
                ORDER BY famiglie.nome_famiglia ASC, persone.data_nascita ASC"
            )
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
              WHERE famiglie_persone.stato = '1'
              GROUP BY famiglie_persone.posizione_famiglia,  persone.sesso"
            )
        );
        return $gruppi;
    }
}
