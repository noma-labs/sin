<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Models;

use App\Nomadelfia\PopolazioneNomadelfia\QueryBuilders\PopolazioneQueryBuilder;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * @property int $persona_id
 * @property string $data_entrata
 * @property string $data_uscita
 *                               Deprecated: use the PopolazioneAttuale model instead.
 */
final class PopolazioneNomadelfia extends Model
{
    public $timestamps = true;

    protected $connection = 'db_nomadelfia';

    protected $table = 'popolazione';

    protected $guarded = [];

    public static function maggiorenni(string $orderby = 'nominativo', string $order = 'ASC'): stdClass
    {
        $magg = self::daEta(18, $orderby, $order);
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy('sesso');
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    public static function daEta(int $eta, string $orderBy = 'nominativo', string $order = 'ASC')
    {
        return DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw('persone.*, popolazione.*')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->whereNull('popolazione.data_uscita')
            ->where('persone.data_nascita', '<=', Carbon::now()->subYears($eta))
            ->orderByRaw('persone.'.$orderBy.' '.$order)
            ->get();
    }

    /*
    *  Ritorna i nomadelfi effettivi  della popolazione divisi per uomini e donne
    *
    */
    public static function effettivi(): stdClass
    {
        $result = new stdClass;
        $effettivi = collect(self::byPosizione('EFFE'));
        $sesso = $effettivi->groupBy('sesso');
        $result->total = $effettivi->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    /*
    *  Ritorna i postulanti  della popolazione
    */
    public static function postulanti(): stdClass
    {
        $result = new stdClass;
        $postulanti = collect(self::byPosizione('POST'));
        $sesso = $postulanti->groupBy('sesso');
        $result->total = $postulanti->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    /*
  *  Ritorna gli ospiti  della popolazione
  */
    public static function ospiti(): stdClass
    {
        $result = new stdClass;
        $ospiti = collect(self::byPosizione('OSPP'));
        $sesso = $ospiti->groupBy('sesso');
        $result->total = $ospiti->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    /*
    *  Ritorna i figli  della popolazione
    */
    public static function figli()
    {
        return self::byPosizione('FIGL');
    }

    /*
  *  Ritorna i figli maggiorenni
  */
    public static function figliMaggiorenni(string $orderby = 'nominativo'): stdClass
    {
        $magg = self::figliDaEta(18, null, $orderby);
        $result = new stdClass;
        $maggioreni = collect($magg);

        $sesso = $maggioreni->groupBy('sesso');
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    /*
    *  Ritorna i minorenni
    */
    public static function figliMinorenni(): stdClass
    {
        $magg = self::figliDaEta(0, 18, 'nominativo');
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy('sesso');
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    /*
    *  Ritorna i minorenni divisi per anno di eta.
    */
    public static function figliMinorenniPerAnno(): stdClass
    {
        $minorenni = collect(self::figliDaEta(0, 18, 'nominativo'));
        $result = new stdClass;
        $result->total = $minorenni->count();
        $minorenni->map(fn ($item, $key) => $item->anno = Carbon::parse($item->data_nascita)->year);
        $groupMinorenni = $minorenni->sortBy(fn ($persona, $key) => $persona->anno);
        $result->anno = $groupMinorenni->groupby([
            'anno',
            fn ($item) => $item->sesso,
        ]);

        return $result;
    }

    /*
     *  Ritorna i figlio fra 18 e 21 anni
     */
    public static function figliFra18e21(): stdClass
    {
        $magg = self::figliDaEta(18, 21, 'nominativo');
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy('sesso');
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    public static function figliMaggiori21(): stdClass
    {
        $magg = self::figliDaEta(21, null, 'nominativo');
        $result = new stdClass;
        $maggioreni = collect($magg);
        $sesso = $maggioreni->groupBy('sesso');
        $result->total = $maggioreni->count();
        $result->uomini = $sesso->get('M', []);
        $result->donne = $sesso->get('F', []);

        return $result;
    }

    /*
    *  Ritorna i sacerdoti della popolazione
    */
    public static function sacerdoti()
    {
        return self::byStati('SAC');
    }

    /*
    *  Ritorna le mamme di vocazione  della popolazione
    */
    public static function mammeVocazione()
    {
        return self::byStati('MAV');
    }

    /*
  *  Ritorna le nomadelfa mamma  della popolazione
  */
    public static function nomadelfaMamma()
    {
        return self::byStati('MAM');
    }

    /*
    *  Ritorna le persone con una certa posizione.
    *  LA posizione è una tra "DADE", "EFFE", "FIGL", "OSPP" "POST"
    */
    public static function byPosizione(string $posizione, $ordeby = 'nominativo')
    {  // "persone_posizioni.data_inizio ASC, persone.nominativo"

        $posizioni = DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw('persone.*, persone_posizioni.*')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->join('persone_posizioni', 'persone_posizioni.persona_id', '=', 'persone.id')
            ->join('posizioni', 'posizioni.id', '=', 'persone_posizioni.posizione_id')
            ->whereNull('popolazione.data_uscita')
            ->where('persone_posizioni.stato', '=', '1')
            ->where('posizioni.abbreviato', '=', $posizione)
            ->orderByRaw($ordeby)
            ->get();

        return $posizioni;
    }

    /*
  *  Ritorna le persone con una certo stato.
  *  Lo stato è una tra "CDE", "CEL", "MAM", "NUB" "MAN", "SAC", "SPO", "VEDE
  */
    public static function byStati(string $stato, $orderby = 'nominativo')
    {  // persone_stati.data_inizio ASC, persone.nominativo"
        $stati = DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw('persone.*, persone_stati.*')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->join('persone_stati', 'persone_stati.persona_id', '=', 'persone.id')
            ->join('stati', 'stati.id', '=', 'persone_stati.stato_id')
            ->whereNull('popolazione.data_uscita')
            ->where('persone_stati.stato', '=', '1')
            ->where('stati.stato', '=', $stato)
            ->whereRaw('persone.deleted_at IS NULL')
            ->orderByRaw($orderby)
            ->get();

        return $stati;
    }

    /*
    *  Ritorna il numero per persone attive per ogni posizione (postulante, effettivo, ospite, figlio)
    */
    public static function perPosizioni()
    {
        $expression = DB::raw(
            "SELECT posizioni.nome, count(*) as count
                FROM persone
                INNER JOIN persone_posizioni ON persone_posizioni.persona_id = persone.id
                INNER JOIN posizioni ON posizioni.id = persone_posizioni.posizione_id
                WHERE persone_posizioni.stato = '1'  AND persone.deleted_at is NULL
                group by posizioni.nome
                ORDER BY posizioni.ordinamento"
        );

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        );
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
        ?int $toEta = null,
        string $orderBy = 'nominativo',
        ?int $travel_to_year = null,
        $withInYear = false
    ) {
        $date = ($travel_to_year === null ? Carbon::now() : Carbon::now()->setYear($travel_to_year));

        $posizione = Posizione::perNome('figlio');
        $end = $date->copy()->subYears($fromEta);
        if ($withInYear) {
            $end = $end->endOfYear();
        }
        $start = $date->copy()->subYears($toEta);
        if ($withInYear) {
            $start = $start->endOfYear();
        }

        $q = DB::connection('db_nomadelfia')
            ->table('persone')
            ->selectRaw('persone.*, persone_posizioni.*')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->join('persone_posizioni', 'persone_posizioni.persona_id', '=', 'persone.id')
            ->join('posizioni', 'persone_posizioni.posizione_id', '=', 'posizioni.id')
            ->whereNull('popolazione.data_uscita')
            ->where('persone_posizioni.stato', '=', '1')
            ->where('persone.data_nascita', '<=', $end)
            ->where('posizioni.abbreviato', '=', $posizione->abbreviato)
            ->orderByRaw($orderBy);
        if ($toEta !== null) {
            $q->where('persone.data_nascita', '>=', $start);
        }

        return $q->get();
    }

    /*
    *  Ritorna la famiglie e i componenti nel nucleo familiare che vivono attualmente in Nomadelfia.
    *
    */
    public static function famiglie()
    {
        $expression = DB::raw(
            "SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita
                FROM persone
                INNER JOIN famiglie_persone ON famiglie_persone.persona_id = persone.id
                INNER JOIN popolazione on popolazione.persona_id = famiglie_persone.persona_id
                LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id
                WHERE popolazione.data_uscita IS NULL
                    AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL)
                    AND (famiglie_persone.posizione_famiglia != 'SINGLE' OR famiglie_persone.stato IS NULL)
                ORDER BY famiglie.nome_famiglia ASC, persone.data_nascita ASC"
        );
        $famiglie = DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        );

        return collect($famiglie)->groupBy('famiglia_id');
    }

    /*
    *  Ritorna il numero di persone per ogni posizione nella fmaiglia (maschi e femmine)
    */
    public static function posizioneFamigliaCount()
    {
        $expression = DB::raw(
            "SELECT famiglie_persone.posizione_famiglia, persone.sesso, count(*) as count
              FROM famiglie_persone
              INNER JOIN persone ON famiglie_persone.persona_id = persone.id
              INNER JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id
              WHERE famiglie_persone.stato = '1'
              GROUP BY famiglie_persone.posizione_famiglia,  persone.sesso"
        );

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        );
    }

    public function newEloquentBuilder($query): PopolazioneQueryBuilder
    {
        return new PopolazioneQueryBuilder($query);
    }
}
