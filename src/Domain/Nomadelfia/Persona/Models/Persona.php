<?php

namespace Domain\Nomadelfia\Persona\Models;

use App\Nomadelfia\Exceptions\CouldNotAssignAzienda;
use App\Nomadelfia\Exceptions\CouldNotAssignIncarico;
use App\Nomadelfia\Exceptions\PersonaHasMultipleFamigliaAttuale;
use App\Nomadelfia\Exceptions\PersonaHasMultipleGroup;
use App\Nomadelfia\Exceptions\PersonaHasMultiplePosizioniAttuale;
use App\Nomadelfia\Exceptions\PersonaHasMultipleStatoAttuale;
use App\Nomadelfia\Exceptions\SpostaNellaFamigliaError;
use App\Patente\Models\Patente;
use App\Traits\SortableTrait;
use Carbon;
use Database\Factories\PersonaFactory;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $data_decesso
 * @property string $data_nascita
 * @property string $cognome
 * @property string $nome
 * @property string $nominativo
 * @property string $sesso
 * @property string $numero_elenco
 * @property string $provincia_nascita
 * @property string $cf
 * @property string $biografia
 */
class Persona extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    protected $connection = 'db_nomadelfia';

    protected $table = 'persone';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $guarded = [];

    protected static function newFactory()
    {
        return PersonaFactory::new();
    }

    public function setNomeAttribute($value): void
    {
        $this->attributes['nome'] = ucwords(strtolower($value));
    }

    public function setCognomeAttribute($value): void
    {
        $this->attributes['cognome'] = ucwords(strtolower($value));
    }

    /**
     * Set the nominativo in uppercase when a new persona is insereted.
     */
    public function setNominativoAttribute($value): void
    {
        $this->attributes['nominativo'] = ucwords(strtolower($value));
    }

    public function getNominativoAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function buildCompleteName()
    {
        Carbon\Carbon::createFromFormat('Y-m-d', $this->data_nascita)->year;

        return "($this->>year) $this->nominativo ($this->nome  $this->cognome)";
    }

    public function getInitialLetterOfCogonome()
    {
        return Str::substr($this->cognome, 0, 1);
    }

    public function anni()
    {
        return Carbon::now()->diffInYears(Carbon::parse($this->data_nascita));
    }

    public function isDeceduta()
    {
        return $this->data_decesso != null;
    }

    public function isMaschio()
    {
        return $this->sesso == 'M';
    }

    public static function NumeroElencoPrefixByLetter(string $lettera)
    {
        return DB::connection('db_nomadelfia')
            ->table('persone')
            ->select(DB::raw('persone.nome, persone.cognome, persone.numero_elenco, CAST(right(numero_elenco, length(numero_elenco)-1) as integer) as numero'))
            ->whereRaw('numero_elenco is not null AND numero_elenco REGEXP :regex and left(numero_elenco,1) = :letter and persone.deleted_at is null', ['regex' => '^[a-zA-Z].*[0-9]$', 'letter' => $lettera])
            ->orderBy('numero', 'DESC');
    }

    /**
     * @throws Exception
     */
    public function proposeNumeroElenco()
    {
        if ($this->numero_elenco) {
            throw new Exception('La persona '.$this->nominativo.' ha già un numero di elenco '.$this->numero_elenco);
        }
        $firstLetter = Str::substr($this->cognome, 0, 1);
        $res = $this->select(DB::raw('left(numero_elenco,1) as  lettera, CAST(right(numero_elenco, length(numero_elenco)-1) as integer)  as numero'))
            ->whereRaw('numero_elenco is not null AND numero_elenco REGEXP ? and left(numero_elenco,1) = ?', ['^[a-zA-Z].*[0-9]$', $firstLetter])
            ->orderBy('numero', 'DESC')
            ->first();
        if ($res) {
            $new = (int) $res->numero + 1;

            return $res->lettera.$new;
        }

        return $firstLetter.'1';

    }

    public function scopeMaggiorenni($query)
    {
        $date = Carbon::now()->subYears(18)->toDatestring();

        return $query->where('data_nascita', '<=', $date);
    }

    public function scopeMinorenni($query)
    {
        $date = Carbon::now()->subYears(18)->toDatestring();

        return $query->where('data_nascita', '>', $date);
    }

    public function scopeDonne($query)
    {
        return $query->where('sesso', 'F');
    }

    public function scopeUomini($query)
    {
        return $query->where('sesso', 'M');
    }

    /**
     * Ritorna le persone che hanno gli anni maggiori o uguali di $eta.
     *
     * @author Davide Neri
     **/
    public function scopeDaEta($query, int $eta, string $orderBy = 'nominativo', $travel_to_year = null)
    {
        $date = ($travel_to_year == null ? Carbon::now() : Carbon::now()->setYear($travel_to_year));
        $end = $date->subYears($eta);

        return $query->where('data_nascita', '<=', $end)->orderby($orderBy);
    }

    /**
     * Ritorna le persone che hanno un eta compresa tra da $frometa e $toeta.
     *
     * @author Davide Neri
     **/
    public function scopeFraEta(
        $query,
        int $frometa,
        int $toeta,
        string $orderBy = 'nominativo',
        $travel_to_year = null,
        $withInYear = false
    ) {
        $date = ($travel_to_year == null ? Carbon::now() : Carbon::now()->setYear($travel_to_year));
        $end = $date->copy()->subYears($frometa);
        if ($withInYear) {
            $end = $end->endOfYear();
        }
        $start = $date->copy()->subYears($toeta);
        if ($withInYear) {
            $start = $start->endOfYear();
        }
        $fromdata = $start->toDateString();
        $todata = $end->toDateString();

        return $query->whereBetween('data_nascita', [$fromdata, $todata])->orderBy($orderBy);
    }

    public function scopeNatiInAnno($query, int $anno)
    {
        return $query->whereRaw('YEAR(data_nascita)= ?', [$anno]);
    }

    public function patenti(): HasMany
    {
        return $this->hasMany(Patente::class, 'persona_id', 'id');
    }

    public function nominativiStorici(): HasOne
    {
        return $this->hasOne(NominativoStorico::class, 'persona_id', 'id');
    }

    // GRUPPO FAMILIARE
    public function gruppifamiliari(): BelongsToMany
    {
        return $this->belongsToMany(GruppoFamiliare::class, 'gruppi_persone', 'persona_id', 'gruppo_famigliare_id')
            ->withPivot('data_entrata_gruppo', 'data_uscita_gruppo', 'stato');
    }

    public function gruppofamiliareAttuale()
    {
        $gruppo = $this->gruppifamiliari()->wherePivot('stato', '1')->get();
        if ($gruppo->count() == 1) {
            return $gruppo[0];
        } elseif ($gruppo->count() == 0) {
            return null;
        } else {
            throw PersonaHasMultipleGroup::named($this);
        }
    }

    public function gruppofamiliariStorico(): BelongsToMany
    {
        return $this->gruppifamiliari()->wherePivot('stato', '0');
    }

    /*
    * Assegna un nuovo gruppo familiare con la data di inzio.
    * Se la persona vive già in un gruppo familiare questo viene concluso usando come data di fine
    * la data di inizio se la data di attuale_data_fine è null.
    *
    */
    public function assegnaGruppoFamiliare($gruppo, $data_inizio, $attuale_data_fine = null): void
    {
        /*if ($this->isCapoFamiglia()){
            $gruppo = GruppoFamiliare::findOrFail($gruppo);
        }*/
        if (is_string($gruppo) || is_int($gruppo)) {
            $gruppo = GruppoFamiliare::findOrFail($gruppo);
        }
        if ($gruppo instanceof GruppoFamiliare) {
            DB::connection('db_nomadelfia')->beginTransaction();
            try {
                $attuale = $this->gruppofamiliareAttuale();
                if ($attuale) {
                    $this->gruppifamiliari()->updateExistingPivot($attuale->id, [
                        'stato' => '0',
                        'data_uscita_gruppo' => ($attuale_data_fine ? $attuale_data_fine : $data_inizio),
                    ]);
                }
                $this->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_inizio]);
                DB::connection('db_nomadelfia')->commit();
            } catch (\Exception $e) {
                DB::connection('db_nomadelfia')->rollback();
                throw $e;
            }
        } else {
            throw new Exception('Bad Argument. Gruppo familiare must be an id or a model.');
        }
    }

    public function concludiGruppoFamiliare($gruppo_id, $datain, $dataout)
    {
        $expression = DB::raw("UPDATE gruppi_persone
               SET stato = '0', data_uscita_gruppo = :dataout
               WHERE gruppo_famigliare_id  = :gruppo AND persona_id = :persona AND data_entrata_gruppo = :datain");

        return DB::connection('db_nomadelfia')->update(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['persona' => $this->id, 'gruppo' => $gruppo_id, 'datain' => $datain, 'dataout' => $dataout]
        );
    }

    public function updateDataInizioGruppoFamiliare($gruppo_id, $currentDatain, $newDataIn)
    {
        $expression = DB::raw('UPDATE gruppi_persone
               SET  data_entrata_gruppo = :new
               WHERE gruppo_famigliare_id  = :gruppo AND persona_id = :persona AND data_entrata_gruppo = :current');

        return DB::connection('db_nomadelfia')->update(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['persona' => $this->id, 'gruppo' => $gruppo_id, 'current' => $currentDatain, 'new' => $newDataIn]
        );
    }

    /**
     * Sposta una persona da un gruppo familiare a un altro..
     *
     * @author Davide Neri
     **/
    public function spostaPersonaInGruppoFamiliare(
        $gruppo_id_current,
        $datain_current,
        $dataout_current,
        $gruppo_id_new,
        $datain_new
    ): void {
        $persona_id = $this->id;

        DB::transaction(function () use (
            &$persona_id,
            &$gruppo_id_current,
            &$datain_current,
            &$dataout_current,
            &$gruppo_id_new,
            &$datain_new
        ): void {
            // disabilita il gruppo attuale
            $expression = DB::raw("UPDATE gruppi_persone
                 SET gruppi_persone.stato = '0', data_uscita_gruppo = :dataout
                 WHERE persona_id = :p  AND gruppo_famigliare_id = :g AND data_entrata_gruppo = :datain
                ");
            DB::connection('db_nomadelfia')->update(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                [
                    'p' => $persona_id,
                    'g' => $gruppo_id_current,
                    'datain' => $datain_current,
                    'dataout' => $dataout_current,
                ]
            );
            // disabilita tutti i gruppi familiare della persona
            $expression = DB::raw("UPDATE gruppi_persone
                SET gruppi_persone.stato = '0'
                WHERE persona_id = :p
                ");
            DB::connection('db_nomadelfia')->update(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                ['p' => $persona_id]
            );

            // assegna il nuovo gruppo alla persona
            $expression = DB::raw("INSERT INTO gruppi_persone (persona_id, gruppo_famigliare_id, stato, data_entrata_gruppo)
                VALUES (:persona, :gruppo, '1', :datain) ");
            DB::connection('db_nomadelfia')->insert(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                ['persona' => $persona_id, 'gruppo' => $gruppo_id_new, 'datain' => $datain_new]
            );
        });
    }

    // AZIENDE
    public function aziende(): BelongsToMany
    {
        return $this->belongsToMany(Azienda::class, 'aziende_persone', 'persona_id', 'azienda_id')
            ->withPivot('data_inizio_azienda', 'data_fine_azienda', 'mansione', 'stato')
            ->where('tipo', 'azienda')
            ->orderby('nome_azienda');
    }

    public function aziendeAttuali(): BelongsToMany
    {
        return $this->aziende()->wherePivotIn('stato', ['Attivo', 'Sospeso']);
    }

    public function aziendeStorico(): BelongsToMany
    {
        return $this->aziende()->wherePivot('stato', 'Non attivo');
    }

    public function assegnaLavoratoreAzienda($azienda, $data_inizio)
    {
        return $this->assegnaAzienda($azienda, $data_inizio, 'LAVORATORE');
    }

    public function assegnaResponsabileAzienda($azienda, $data_inizio)
    {
        return $this->assegnaAzienda($azienda, $data_inizio, 'RESPONSABILE AZIENDA');
    }

    public function assegnaAzienda($azienda, $data_inizio, $mansione): void
    {
        if (is_string($azienda) || is_int($azienda)) {
            $azienda = Azienda::findOrFail($azienda);
        }
        if (strcasecmp($mansione, 'LAVORATORE') == 0 or strcasecmp($mansione, 'RESPONSABILE AZIENDA') == 0) {
            if ($azienda instanceof Azienda) {
                if (! $azienda->isAzienda()) {
                    throw CouldNotAssignAzienda::isNotValidAzienda($azienda);
                }
                if ($this->aziendeAttuali->contains($azienda->id)) { // la persona è stata già asseganta all'azienda
                    throw CouldNotAssignAzienda::isAlreadyWorkingIntozienda($azienda, $this);
                }
                $this->aziende()->attach($azienda->id, [
                    'stato' => 'Attivo',
                    'data_inizio_azienda' => $data_inizio,
                    'mansione' => $mansione,
                ]);
            } else {
                throw new Exception('Bad Argument. Azienda must be the id or a model.');
            }
        } else {
            throw CouldNotAssignAzienda::mansioneNotValid($mansione);
        }
    }

    // Incarichi
    public function incarichi(): BelongsToMany
    {
        return $this->belongsToMany(Incarico::class, 'incarichi_persone', 'persona_id', 'incarico_id')
            ->withPivot('data_inizio', 'data_fine')
            ->orderby('nome');
    }

    public function incarichiAttuali(): BelongsToMany
    {
        return $this->incarichi()->wherePivot('data_fine', null);
    }

    public function incarichiStorico(): BelongsToMany
    {
        return $this->incarichi()->wherePivot('data_fine', 'Non attivo');
    }

    public function incarichiPossibili()
    {
        $multiplied = $this->incarichiAttuali()->get()->pluck('id');
        if ($multiplied != null) {
            return Incarico::whereNotIn('id', $multiplied)->get();
        }

        return $multiplied;
    }

    public function assegnaLavoratoreIncarico(\Domain\Nomadelfia\Incarico\Models\Incarico|string $azienda, Carbon\Carbon $data_inizio)
    {
        return $this->assegnaIncarico($azienda, $data_inizio);
    }

    /**
     * @throws CouldNotAssignIncarico
     * @throws Exception
     */
    public function assegnaIncarico(Incarico|string $incarico, $data_inizio): void
    {
        if (is_string($incarico)) {
            $incarico = Incarico::findOrFail($incarico);
        }
        if (! $incarico instanceof Incarico) {
            throw new Exception('Bad Argument. Incarico must be the id or a model.');
        }
        if ($this->incarichiAttuali()->where('id', $incarico->id)->exists()) { // la persona è stata già l'incarico
            throw CouldNotAssignIncarico::hasAlreadyIncarico($incarico, $this);
        }
        $this->incarichi()->attach($incarico->id, [
            'data_inizio' => $data_inizio,
        ]);
    }

    // CARICHCE
    public function cariche(): BelongsToMany
    {
        return $this->belongsToMany(Azienda::class, 'persone_cariche', 'persona_id', 'cariche_id')
            ->withPivot('data_inizio', 'data_fine')
            ->orderby('nome');
    }

    public function caricheAttuali(): BelongsToMany
    {
        return $this->aziende()->wherePivot('data_fine', '=', null);
    }

    public function caricheStorico(): BelongsToMany
    {
        return $this->aziende()->wherePivot('stato', '!=', null);
    }

    // Popolazione
    public function popolazione(): HasMany
    {
        return $this->hasMany(PopolazioneNomadelfia::class, 'persona_id', 'id');
    }

    public function setDataEntrataNomadelfia($old_data_entrata, $data_entrata)
    {
        $affected = PopolazioneNomadelfia::query()->where('persona_id', $this->id)->where('data_entrata',
            $old_data_entrata)->update(['data_entrata' => $data_entrata]);
        if ($affected > 0) {
            return true;
        }

        return false;
    }

    public function getDataEntrataNomadelfia()
    {

        $pop = PopolazioneNomadelfia::where('persona_id', $this->id)->orderBy('data_entrata', 'DESC')->get();
        if (count($pop) > 0) {
            return $pop->first()->data_entrata;
        }

        return null;
    }

    public function getDataUscitaNomadelfia()
    {

        $pop = PopolazioneNomadelfia::where('persona_id', $this->id)->orderBy('data_uscita', 'DESC')->whereNotNull('data_uscita');
        if ($pop->count() > 0) {
            return $pop->first()->data_uscita;
        }

        return null;

    }

    public function getDataDecesso()
    {
        return $this->data_decesso;
    }

    public function isPersonaInterna(): bool
    {
        $pop = PopolazioneNomadelfia::whereNull('data_uscita')->where('persona_id', $this->id);
        if ($pop->count() > 0) {
            return true;
        }

        return false;
    }

    /*
    * Return True if the person is dead, false otherwise
    */
    public function isDeceduto()
    {
        return $this->data_decesso != null;
    }

    // TODO: move into a dedicated Action that call the UscitaDaNomdelfiaActiongst
    public function deceduto($data_decesso): void
    {
        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $act = app(UscitaPersonaAction::class);
            $act->execute($this, $data_decesso);

            $conn = DB::connection('db_nomadelfia');

            // aggiorna la data di decesso
            $conn->update(
                'UPDATE persone SET data_decesso = ?, updated_at = NOW() WHERE id = ?',
                [$data_decesso, $this->id]
            );

            $conn->insert('UPDATE popolazione SET data_uscita = ? WHERE persona_id = ? AND data_uscita IS NULL',
                [$data_decesso, $this->id]);

            // aggiorna lo stato familiare  con la data di decesso
            $conn->insert(
                "UPDATE persone_stati SET data_fine = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$data_decesso, $this->id]
            );

            // aggiorna la data di uscita dalla famiglia con la data di decesso
            $conn->insert(
                "UPDATE famiglie_persone SET stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$this->id]
            );

            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }

    // STATO
    public function stati(): BelongsToMany
    {
        return $this->belongsToMany(Stato::class, 'persone_stati', 'persona_id', 'stato_id')
            ->withPivot('stato', 'data_inizio', 'data_fine');
    }

    public function statoAttuale()
    {
        $stato = $this->stati()->wherePivot('stato', '1')->get();
        if ($stato->count() == 1) {
            return $stato[0];
        } elseif ($stato->count() == 0) {
            return null;
        } else {
            throw PersonaHasMultipleStatoAttuale::named($this->nominativo);
        }
    }

    public function statiStorico(): BelongsToMany
    {
        return $this->stati()->wherePivot('stato', '0')
            ->orderby('data_fine', 'desc');
    }

    /*
    * Assegna un nuovo stato alla persona.
    * Se la persona ha uno stato attuale viene concluso con la data di inizio del nuovo stato.
    */
    public function assegnaStato($stato, $data_inizio, $attuale_data_fine = null): void
    {
        if (is_string($stato) | is_int($stato)) {
            $stato = Stato::findOrFail($stato);
        }
        if ($stato instanceof Stato) {
            DB::connection('db_nomadelfia')->beginTransaction();
            try {
                $attuale = $this->statoAttuale();
                if ($attuale) {
                    $this->stati()->updateExistingPivot($attuale->id,
                        ['stato' => '0', 'data_fine' => ($attuale_data_fine ? $attuale_data_fine : $data_inizio)]);
                }
                $this->stati()->attach($stato->id, ['stato' => '1', 'data_inizio' => $data_inizio]);
                DB::connection('db_nomadelfia')->commit();
            } catch (\Exception $e) {
                DB::connection('db_nomadelfia')->rollback();
                throw $e;
            }
        } else {
            throw new Exception('Bad Argument. Stato must be an id or a model.');
        }
    }

    public function assegnaSacerdote(Carbon\Carbon $data_inizio, $attuale_data_fine = null): void
    {

        $sacerdote = Stato::perNome('sacerdote');
        $this->assegnaStato($sacerdote, $data_inizio, $attuale_data_fine);
    }

    // FAMIGLIA
    public function famiglie(): BelongsToMany
    {
        return $this->belongsToMany(Famiglia::class, 'famiglie_persone', 'persona_id', 'famiglia_id')
            ->withPivot('stato');
    }

    public function famigliaAttuale()
    {

        $famiglia = $this->famiglie()
            ->wherePivot('stato', '1')
            ->withPivot('posizione_famiglia')
            ->get();
        if ($famiglia->count() == 0) {
            // IF null; the person has no a family so it is a single
            return null;
        }
        if ($famiglia->count() == 1) {
            return $famiglia[0];
        }
        throw PersonaHasMultipleFamigliaAttuale::named($this->nominativo);
    }

    public function famiglieStorico(): BelongsToMany
    {
        return $this->famiglie()
            ->wherePivot('stato', '0')
            ->withPivot('posizione_famiglia');
    }

    // Crea una famiglia e aggiunge la persona come componente
    public function createAndAssignFamiglia($persona_id, $posizione, $nome, $data_creazione)
    {
        try {
            DB::transaction(function () use (&$persona_id, &$posizione, &$nome, &$data_creazione): void {
                $famiglia = Famiglia::create(['nome_famiglia' => $nome, 'data_creazione' => $data_creazione]);

                $famiglia->componenti()->attach($persona_id, [
                    'stato' => '1',
                    'posizione_famiglia' => $posizione,
                ]);
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Move a person to a family.
     * If the person has already an active family, the current family is deactivate.
     */
    public function spostaNellaFamiglia($famiglia, $posizione)
    {
        if ($famiglia->single()) {
            throw SpostaNellaFamigliaError::create($this->nominativo, $famiglia->nome_famiglia,
                'La famiglia single non può avere più di un componente');
        }
        $attuale = $this->famigliaAttuale();
        try {
            if (! $attuale) {
                $this->famiglie()->attach($famiglia->id,
                    ['stato' => '1', 'posizione_famiglia' => $posizione]);
            } else {
                // TODO; check se la persona può essere asseganta alla nuova famiglia
                DB::transaction(function () use (&$attuale, &$famiglia, &$posizione): void {
                    $expression = DB::raw("UPDATE famiglie_persone
                        SET stato = '0'
                        WHERE persona_id  = :persona AND famiglia_id = :famiglia ");
                    DB::connection('db_nomadelfia')->update(
                        $expression->getValue(DB::connection()->getQueryGrammar()),
                        [
                            'persona' => $this->id,
                            'famiglia' => $attuale->id,
                        ]
                    );

                    $this->famiglie()->attach($famiglia->id,
                        ['stato' => '1', 'posizione_famiglia' => $posizione]);
                });
            }
        } catch (\Exception $e) {
            throw SpostaNellaFamigliaError::create($this->nominativo, $famiglia->nome_famiglia, str($e));
        }

        return $this;
    }

    /**
     * Ritorna la posizione di una persona in una famiglia
     *
     * @return bool
     *
     * @author Davide Neri
     **/
    public function famigliaPosizione(string $posizione)
    {
        if ($this->famigliaAttuale()) {
            return $this->famigliaAttuale()->pivot->posizione_famiglia == $posizione;
        } else {
            return false;
        }
    }

    /**
     * Ritorna vero se la persona è maggiorenne
     *
     * @return bool
     *
     * @author Davide Neri
     **/
    public function isMaggiorenne()
    {
        return Carbon::now()->diffInYears(Carbon::parse($this->data_nascita)) > 18;
    }

    /**
     * Ritorna vero se la persona è single altrimenti ritorna falso.
     *
     * @return bool
     *
     * @author Davide Neri
     **/
    public function isSingle()
    {
        return $this->famigliaPosizione('SINGLE');
    }

    /**
     * Ritorna vero se una persona è il capo famiglia altrimenti ritorna falso.
     *
     * @return bool
     *
     * @author Davide Neri
     **/
    public function isCapoFamiglia()
    {
        return $this->famigliaPosizione('CAPO FAMIGLIA');
    }

    /**
     * Ritorna vero se una persona è la moglie altrimenti ritorna falso.
     *
     * @return bool
     *
     * @author Davide Neri
     **/
    public function isMoglie()
    {
        return $this->famigliaPosizione('MOGLIE');
    }

    /**
     * Ritorna vero se una persona è un figlioaccolto altrimenti ritorna falso.
     *
     * @return bool
     *
     * @author Davide Neri
     **/
    public function isFiglio()
    {
        return $this->isFiglioNato() or $this->isFiglioAccolto();
    }

    /**
     * Ritorna vero se una persona è un figlio nato altrimenti ritorna falso.
     *
     * @return bool
     *
     * @author Davide Neri
     **/
    public function isFiglioNato()
    {
        return $this->famigliaPosizione('FIGLIO NATO');
    }

    /**
     * Ritorna vero se una persona è un figlioaccolto altrimenti ritorna falso.
     *
     * @return bool
     *
     * @author Davide Neri
     **/
    public function isFiglioAccolto()
    {
        return $this->famigliaPosizione('FIGLIO ACCOLTO');
    }

    // POSIZIONE
    public function posizioni(): BelongsToMany
    {
        return $this->belongsToMany(Posizione::class, 'persone_posizioni', 'persona_id', 'posizione_id')
            ->withPivot('stato', 'data_inizio', 'data_fine');
    }

    public function posizioneAttuale()
    {
        $posizione = $this->posizioni()->wherePivot('stato', '1')->get();
        if ($posizione->count() == 1) {
            return $posizione[0];
        } elseif ($posizione->count() == 0) {
            return null;
        } else {
            throw PersonaHasMultiplePosizioniAttuale::named($this->nominativo);
        }
    }

    public function isEffettivo(): bool
    {
        $posizione = $this->posizioneAttuale();
        if ($posizione) {
            return $posizione->isEffettivo();
        }

        return false;
    }

    public function posizioniStorico(): BelongsToMany
    {
        return $this->posizioni()->wherePivot('stato', '0');
    }

    public function assegnaPostulante(Carbon\Carbon $data_inizio): void
    {
        // TODO check if the person is ospite before postulante
        //        $attuale = this->posizioneAttuale();
        //        if $attuale && $attuale->isPostulante){
        //            throw new Es
        //        }
        $p = Posizione::perNome('postulante');
        $this->assegnaPosizione($p, $data_inizio);
    }

    public function assegnaNomadelfoEffettivo(
        Carbon\Carbon $data_inizio
    ): void {
        // TODO: check that the posizione attuale è postulante
        //        $attuale = this->posizioneAttuale();
        //        if $attuale && !$attuale->isPostulante){
        //            throw new Es
        //        }
        $effe = Posizione::perNome('effettivo');
        $this->assegnaPosizione($effe, $data_inizio);
    }

    public function assegnaPosizione(
        $posizione,
        string $data_inizio,
        ?string $attuale_data_fine = null
    ): void {
        if (is_string($posizione) || is_int($posizione)) {
            $posizione = Posizione::findOrFail($posizione);
        }
        if ($posizione instanceof Posizione) {
            DB::connection('db_nomadelfia')->beginTransaction();
            try {
                $attuale = $this->posizioneAttuale();
                if ($attuale) {
                    $this->posizioni()->updateExistingPivot($attuale->id,
                        ['stato' => '0', 'data_fine' => ($attuale_data_fine ? $attuale_data_fine : $data_inizio)]);
                }
                $this->posizioni()->attach($posizione->id, ['stato' => '1', 'data_inizio' => $data_inizio]);
                DB::connection('db_nomadelfia')->commit();
            } catch (\Exception $e) {
                DB::connection('db_nomadelfia')->rollback();
                throw $e;
            }
        } else {
            throw new Exception('Bad Argument. Posizione must be an id or a model.');
        }
    }

    public function modificaDataInizioPosizione(
        $posizione_id,
        $currentDatain,
        $newDataIn
    ) {
        $expression = DB::raw('UPDATE persone_posizioni
               SET  data_inizio = :new
               WHERE posizione_id  = :posizone AND persona_id = :persona AND data_inizio = :current');

        return DB::connection('db_nomadelfia')->update(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['posizone' => $posizione_id, 'persona' => $this->id, 'current' => $currentDatain, 'new' => $newDataIn]
        );
    }

    public function concludiPosizione(
        $posizione_id,
        $datain,
        $datafine
    ) {
        $expression = DB::raw("UPDATE persone_posizioni
               SET stato = '0', data_fine = :dataout
               WHERE posizione_id  = :posizone AND persona_id = :persona AND data_inizio = :datain");

        return DB::connection('db_nomadelfia')->update(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['posizone' => $posizione_id, 'persona' => $this->id, 'datain' => $datain, 'dataout' => $datafine]
        );
    }

    /**
     * Ritorna le posizioni assegnabili ad una persona.
     *
     **/
    public function posizioniPossibili()
    {
        $pos = self::posizioneAttuale();
        $posizioni = Posizione::all();
        if ($pos != null && $pos->count() == 1) {
            $pos = $pos->first();
            $posizioni = $posizioni->except([$pos->id]);
            if ($pos->is(Posizione::find('EFFE'))) {
                return $posizioni->except([Posizione::find('FIGL')->id]);
            }
            if ($pos->is(Posizione::find('POST'))) {
                return $posizioni->except([Posizione::find('FIGL')->id]);
            }
            if ($pos->is(Posizione::find('OSPP'))) {
                return $posizioni->except([Posizione::find('EFFE')->id]);
            }
            if ($pos->is(Posizione::find('FIGL'))) {
                return $posizioni->except([Posizione::find('EFFE')->id]);
            }

            return $posizioni;
        } else {
            return $posizioni;
        }
    }

    //***************************************************************************
    //                         Esercizi Spirituali
    //***************************************************************************

    public function eserciziSpirituali(): BelongsToMany
    {
        return $this->belongsToMany(EserciziSpirituali::class, 'persone_esercizi', 'persona_id', 'esercizi_id');
    }

    /**
     * Sposta una persona e la sua famiglia dal gruppo familiare attuale in un nuovo gruppo familiare.
     *
     * @param  int|null  $gruppoFamiliareAttuale
     * @param  Carbon\Carbon  $dataUscitaGruppoFamiliareAttuale
     * @param  int  $gruppoFamiliareNuovo
     * @param  Carbon\Carbon  $dataEntrataGruppo
     */
    public function cambiaGruppoFamiliare(
        $gruppoFamiliareAttuale,
        $dataUscitaGruppoFamiliareAttuale,
        $gruppoFamiliareNuovo,
        $dataEntrataGruppo
    ): void {
        if ($this->isCapoFamiglia() or $this->isSingle()) {
            $this->famigliaAttuale()->assegnaFamigliaANuovoGruppoFamiliare($gruppoFamiliareAttuale,
                $dataUscitaGruppoFamiliareAttuale, $gruppoFamiliareNuovo, $dataEntrataGruppo);
        }
    }

    public function assegnaPersonaANuovoGruppoFamiliare($gruppoFamiliareAttuale, $dataUscitaGruppoFamiliareAttuale, $gruppoFamiliareNuovo, $dataEntrataGruppo = null): void
    {
        $this->gruppifamiliari()->updateExistingPivot($gruppoFamiliareAttuale,
            ['stato' => '0', 'data_uscita_gruppo' => $dataUscitaGruppoFamiliareAttuale]);
        $this->gruppifamiliari()->attach($gruppoFamiliareNuovo,
            ['stato' => '1', 'data_entrata_gruppo' => $dataEntrataGruppo]);
    }
}
