<?php

namespace Domain\Nomadelfia\Persona\Models;

use App\Biblioteca\Models\Prestito;
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
use Domain\Nomadelfia\Persona\QueryBuilders\PersonaQueryBuilder;
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

    public function newEloquentBuilder($query): PersonaQueryBuilder
    {
        return new PersonaQueryBuilder($query);
    }

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

    public function setNominativoAttribute($value): void
    {
        $this->attributes['nominativo'] = ucwords(strtolower($value));
    }

    public function getNominativoAttribute($value): string
    {
        return ucwords(strtolower($value));
    }

    public function buildCompleteName(): string
    {
        $year = Carbon::createFromFormat('Y-m-d', $this->data_nascita)->year;

        return "($year) $this->nome  $this->cognome";
    }

    public function getInitialLetterOfCogonome()
    {
        return Str::substr($this->cognome, 0, 1);
    }

    public function isMaschio(): bool
    {
        return $this->sesso == 'M';
    }

    public function isDeceduta(): bool
    {
        return $this->data_decesso != null;
    }

    public function isMaggiorenne(): bool
    {
        return Carbon::now()->diffInYears(Carbon::parse($this->data_nascita)) > 18;
    }

    // *************
    //  RELATIONSHIPS
    // *************

    public function patenti(): HasMany
    {
        return $this->hasMany(Patente::class, 'persona_id', 'id');
    }

    public function prestiti()
    {
        return $this->hasMany(Prestito::class, 'cliente_id', 'id');
    }

    public function nominativiStorici(): HasOne
    {
        return $this->hasOne(NominativoStorico::class, 'persona_id', 'id');
    }

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

    public function eserciziSpirituali(): BelongsToMany
    {
        return $this->belongsToMany(EserciziSpirituali::class, 'persone_esercizi', 'persona_id', 'esercizi_id');
    }

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


    // *************
    //  OTHER METHODS
    // *************


    public function setDataEntrataNomadelfia($old_data_entrata, $data_entrata): bool
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

    public function isPersonaInterna(): bool
    {
        $pop = PopolazioneNomadelfia::whereNull('data_uscita')->where('persona_id', $this->id);
        if ($pop->count() > 0) {
            return true;
        }

        return false;
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

    // Crea una famiglia e aggiunge la persona come componente
    public function createAndAssignFamiglia($persona_id, $posizione, $nome, $data_creazione): bool
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
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Move a person to a family.
     * If the person has already an active family, the current family is deactivate.
     */
    public function spostaNellaFamiglia($famiglia, $posizione): static
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
