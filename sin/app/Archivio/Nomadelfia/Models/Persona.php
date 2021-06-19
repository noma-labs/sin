<?php

namespace App\Nomadelfia\Models;

use App\Nomadelfia\Exceptions\PersonaHasMultipleCategorieAttuale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon;
use Exception;
use App\Nomadelfia\Exceptions\SpostaNellaFamigliaError;
use Illuminate\Support\Facades\DB;

use App\Traits\SortableTrait;
use Illuminate\Support\Str;

use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Incarico;
use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\EserciziSpirituali;


use App\Nomadelfia\Exceptions\GruppoFamiliareDoesNOtExists;
use App\Nomadelfia\Exceptions\PersonaHasMultiplePosizioniAttuale;
use App\Nomadelfia\Exceptions\PersonaHasMultipleStatoAttuale;
use App\Nomadelfia\Exceptions\PersonaHasMultipleFamigliaAttuale;
use App\Nomadelfia\Exceptions\PersonaErrors;
use App\Nomadelfia\Exceptions\PersonaIsMinorenne;
use App\Nomadelfia\Exceptions\PersonaHasMultipleGroup;
use App\Nomadelfia\Exceptions\PersonaHasNoGroup;
use App\Nomadelfia\Exceptions\FamigliaHasNoGroup;

use App\Patente\Models\Patente;
use App\Nomadelfia\Models\Stato;
use App\Nomadelfia\Models\Categoria;
use App\Admin\Models\Ruolo;

class Persona extends Model
{
    use SortableTrait;
    use SoftDeletes;


    protected $connection = 'db_nomadelfia';
    protected $table = 'persone';
    protected $primaryKey = "id";

    public $timestamps = true;
    protected $guarded = [];

    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = ucwords(strtolower($value));
    }

    public function setCognomeAttribute($value)
    {
        $this->attributes['cognome'] = ucwords(strtolower($value));
    }

    /**
     * Set the nominativo in uppercase when a new persona is insereted.
     */
    public function setNominativoAttribute($value)
    {
        $this->attributes['nominativo'] = ucwords(strtolower($value));
    }

    public function getNominativoAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    /**
     * Returns only the people that are currently living in Nomadelfia.
     */
    public function scopePresente($query)
    {
        return $query->where('categoria_id', "<", 4);
    }

    public function scopeAttivo($query)
    {
        return $query->where('persone.stato', '1');
    }


    public function isAttivo()
    {
        return $this->stato == "1";
    }

    public function isDeceduta()
    {
        return $this->data_decesso != null;
    }

    public function isMaschio()
    {
        return $this->sesso == "M";
    }

    public function scopeMaggiorenni($query)
    {
        $date = Carbon::now()->subYears(18)->toDatestring();
        return $query->where('data_nascita', "<=", $date);
    }

    public function scopeMinorenni($query)
    {
        $date = Carbon::now()->subYears(18)->toDatestring();
        return $query->where('data_nascita', ">", $date);
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
     * @param int $eta
     * @author Davide Neri
     **/
    public function scopeDaEta($query, int $eta)
    {
        $data = Carbon::now()->subYears($eta)->toDateString();
        return $query->where('data_nascita', '<=', $data);
    }

    /**
     * Ritorna le persone che hanno un eta compresa tra da $frometa e  $toeta.
     * @param int $frometa
     * @param int $toeta
     * @author Davide Neri
     **/
    public function scopeFraEta($query, int $frometa, int $toeta)
    {
        $fromdata = Carbon::now()->subYears($toeta)->toDateString();
        $todata = Carbon::now()->subYears($frometa)->toDateString();
        return $query->whereBetween('data_nascita', [$fromdata, $todata]);
    }


    public function patenti()
    {
        return $this->hasMany(Patente::class, 'persona_id', 'id');
    }

    public function nominativiStorici()
    {
        return $this->hasOne(NominativoStorico::class, 'persona_id', 'id');
    }


    // GRUPPO FAMILIARE
    public function gruppifamiliari()
    {
        return $this->belongsToMany(GruppoFamiliare::class, 'gruppi_persone', 'persona_id', 'gruppo_famigliare_id')
            ->withPivot("data_entrata_gruppo", "data_uscita_gruppo", 'stato');
    }

    public function gruppofamiliareAttuale()
    {
        $gruppo = $this->gruppifamiliari()->wherePivot('stato', '1')->get();
        if ($gruppo->count() == 1) {
            return $gruppo[0];
        } elseif ($gruppo->count() == 0) {
            return null;
        } else {
            throw PersonaHasMultipleGroup::named($this->nominativo);
        }
    }


    public function gruppofamiliariStorico()
    {
        return $this->gruppifamiliari()->wherePivot('stato', '0');
    }

    /*
    * Assegna un nuovo gruppo familiare con la data di inzio.
    * Se la persona vive già in un gruppo familiare questo viene concluso usando come data di fine
    * la data di inizio se la data di attuale_data_fine è null.
    *
    */
    public function assegnaGruppoFamiliare($gruppo, $data_inizio, $attuale_data_fine = null)
    {
        /*if ($this->isCapoFamiglia()){
            $gruppo = GruppoFamiliare::findOrFail($gruppo);
        }*/
        if (is_string($gruppo)) {
            $gruppo = GruppoFamiliare::findOrFail($gruppo);
        }
        if ($gruppo instanceof GruppoFamiliare) {
            DB::connection('db_nomadelfia')->beginTransaction();
            try {
                $attuale = $this->gruppofamiliareAttuale();
                if ($attuale) {
                    $this->gruppifamiliari()->updateExistingPivot($attuale->id, [
                        'stato' => '0',
                        'data_uscita_gruppo' => ($attuale_data_fine ? $attuale_data_fine : $data_inizio)
                    ]);
                }
                $this->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_inizio]);
                DB::connection('db_nomadelfia')->commit();
            } catch (\Exception $e) {
                DB::connection('db_nomadelfia')->rollback();
                throw $e;
            }
        } else {
            throw new Exception("Bad Argument. Gruppo familiare must be an id or a model.");
        }
    }

    public function concludiGruppoFamiliare($gruppo_id, $datain, $dataout)
    {
        $res = DB::connection('db_nomadelfia')->update(
            DB::raw("UPDATE gruppi_persone
               SET stato = '0', data_uscita_gruppo = :dataout
               WHERE gruppo_famigliare_id  = :gruppo AND persona_id = :persona AND data_entrata_gruppo = :datain"),
            array('persona' => $this->id, "gruppo" => $gruppo_id, "datain" => $datain, "dataout" => $dataout)
        );
        return $res;
    }

    public function updateDataInizioGruppoFamiliare($gruppo_id, $currentDatain, $newDataIn)
    {
        $res = DB::connection('db_nomadelfia')->update(
            DB::raw("UPDATE gruppi_persone
               SET  data_entrata_gruppo = :new
               WHERE gruppo_famigliare_id  = :gruppo AND persona_id = :persona AND data_entrata_gruppo = :current"),
            array('persona' => $this->id, "gruppo" => $gruppo_id, "current" => $currentDatain, "new" => $newDataIn)
        );
        return $res;
    }


    /**
     * Sposta una persona da un gruppo familiare a un altro..
     * @author Davide Neri
     **/
    public function spostaPersonaInGruppoFamiliare(
        $gruppo_id_current,
        $datain_current,
        $dataout_current,
        $gruppo_id_new,
        $datain_new
    ) {
        $persona_id = $this->id;
        return DB::transaction(function () use (
            &$persona_id,
            &$gruppo_id_current,
            &$datain_current,
            &$dataout_current,
            &$gruppo_id_new,
            &$datain_new
        ) {
            // disabilita il gruppo attuale
            DB::connection('db_nomadelfia')->update(
                DB::raw("UPDATE gruppi_persone
                 SET gruppi_persone.stato = '0', data_uscita_gruppo = :dataout
                 WHERE persona_id = :p  AND gruppo_famigliare_id = :g AND data_entrata_gruppo = :datain
                "),
                array(
                    'p' => $persona_id,
                    'g' => $gruppo_id_current,
                    "datain" => $datain_current,
                    'dataout' => $dataout_current
                )
            );
            // disabilita tutti i gruppi familiare della persona
            DB::connection('db_nomadelfia')->update(
                DB::raw("UPDATE gruppi_persone
                SET gruppi_persone.stato = '0'
                WHERE persona_id = :p
                "),
                array('p' => $persona_id)
            );

            // assegna il nuovo gruppo alla persona
            DB::connection('db_nomadelfia')->insert(
                DB::raw("INSERT INTO gruppi_persone (persona_id, gruppo_famigliare_id, stato, data_entrata_gruppo)
                VALUES (:persona, :gruppo, '1', :datain) "),
                array('persona' => $persona_id, 'gruppo' => $gruppo_id_new, 'datain' => $datain_new)
            );
        });
    }

    // AZIENDE
    public function aziende()
    {
        return $this->belongsToMany(Azienda::class, 'aziende_persone', 'persona_id', 'azienda_id')
            ->withPivot('data_inizio_azienda', 'data_fine_azienda', 'mansione', 'stato')
            ->orderby("nome_azienda");
    }

    public function aziendeAttuali()
    {
        return $this->aziende()->wherePivotIn('stato', ['Attivo', 'Sospeso']);
    }

    public function aziendeStorico()
    {
        return $this->aziende()->wherePivot('stato', 'Non attivo');
    }


    // CATEGORIA

    public function categorie(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'persone_categorie', 'persona_id', 'categoria_id')
            ->withPivot('data_inizio', 'data_fine', 'stato');
    }

    public function categoriaAttuale()
    {
//        return $this->categorie()->wherePivot('stato', '1')->first();
        $categoria = $this->categorie()->wherePivot('stato', '1')->get();
        if ($categoria->count() == 1) {
            return $categoria[0];
        } elseif ($categoria->count() == 0) {
            return null;
        } else {
            throw PersonaHasMultipleCategorieAttuale::named($this->nominativo);
        }
    }

    public function categorieStorico(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->categorie()->wherePivot('stato', '0')
            ->orderby('data_fine', 'desc');
    }


    // Inserisce un minorenne che entra con la sua famiglia
    public function entrataMinorenneConFamiglia($data_entrata, $famiglia_id)
    {
        $famiglia = Famiglia::findOrFail($famiglia_id);
        $gruppo = $famiglia->gruppoFamiliareAttualeOrFail();

        $pos = Posizione::find("FIGL");
        if ($this->isMaschio()) {
            $stato = Stato::find("CEL");
        } else {
            $stato = Stato::find("NUB");
        }
        $famiglia_data = $this->data_nascita; // la data di entrata nella famiglia è uguale alla data di nascita
        $gruppo_data = $data_entrata;
        $pos_data = $data_entrata;
        $stato_data = $this->data_nascita;
        $this->entrataInNomadelfia($data_entrata, $pos->id, $pos_data, $gruppo->id, $gruppo_data, $stato->id,
            $stato_data, $famiglia_id, "FIGLIO NATO", $famiglia_data);
    }

    // Inserissce un minorenne che entra come figlio accolto
    public function entrataMinorenneAccolto($data_entrata, $famiglia_id)
    {
        $famiglia = Famiglia::findOrFail($famiglia_id);
        $gruppo = $famiglia->gruppoFamiliareAttualeOrFail();

        $pos = Posizione::find("FIGL");
        if ($this->isMaschio()) {
            $stato = Stato::find("CEL");
        } else {
            $stato = Stato::find("NUB");
        }
        $famiglia_data = $data_entrata;  // la data di entrata nella famiglia è uguale alla data di entrata in nomadelfia
        $gruppo_data = $data_entrata;
        $pos_data = $data_entrata;
        $stato_data = $this->data_nascita;
        $this->entrataInNomadelfia($data_entrata, $pos->id, $pos_data, $gruppo->id, $gruppo_data, $stato->id,
            $stato_data, $famiglia_id, "FIGLIO ACCOLTO", $famiglia_data);
    }

    public function entrataNatoInNomadelfia($famiglia_id)
    {
        $famiglia = Famiglia::findOrFail($famiglia_id);
        $gruppo = $famiglia->gruppoFamiliareAttualeOrFail();

        $pos = Posizione::find("FIGL");
        if ($this->isMaschio()) {
            $stato = Stato::find("CEL");
        } else {
            $stato = Stato::find("NUB");
        }
        $famiglia_data = $this->data_nascita;
        $gruppo_data = $this->data_nascita;
        $pos_data = $this->data_nascita;
        $stato_data = $this->data_nascita;
        $this->entrataInNomadelfia($this->data_nascita, $pos->id, $pos_data, $gruppo->id, $gruppo_data, $stato->id,
            $stato_data, $famiglia_id, "FIGLIO NATO", $famiglia_data);
    }

    public function entrataMaggiorenneSingle($data_entrata, $gruppo_id)
    {
        if (!$this->isMaggiorenne()) {
            throw PersonaIsMinorenne::named($this->nominativo);
        }

        $pos = Posizione::find("OSPP");
        if ($this->isMaschio()) {
            $stato = Stato::find("CEL");
        } else {
            $stato = Stato::find("NUB");
        }
        $gruppo_data = $data_entrata;
        $pos_data = $data_entrata;
        $stato_data = $this->data_nascita;

        $fam_data = Carbon::parse($this->data_nascita)->addYears(18)->toDatestring();
        $nome_famiglia = $this->nome . " " . Str::substr($this->cognome, 0, 2);
        $fam = Famiglia::firstOrCreate(['nome_famiglia' => $nome_famiglia], ['data_creazione' => $fam_data]);

        $this->entrataInNomadelfia($data_entrata, $pos->id, $pos_data, $gruppo_id, $gruppo_data, $stato->id,
            $stato_data, $fam->id, "SINGLE", $fam_data);
    }

    public function entrataMaggiorenneSposato($data_entrata, $gruppo_id)
    {
        if (!$this->isMaggiorenne()) {
            throw PersonaIsMinorenne::named($this->nominativo);
        }
        $pos = Posizione::find("OSPP");
        $gruppo_data = $data_entrata;
        $pos_data = $data_entrata;
        $stato_data = $this->data_nascita;
        $this->entrataInNomadelfia($data_entrata, $pos->id, $pos_data, $gruppo_id, $gruppo_data);
    }

    // Inserisce una persona nella comunità per la prima volta.
    public function entrataInNomadelfia(
        $data,
        $posizione_id,
        $posizione_data,
        $gruppo_id,
        $gruppo_data,
        $stato_id = null,
        $stato_data = null,
        $famiglia_id = null,
        $famiglia_posizione = null,
        $famiglia_data = null
    ) {
        // TODO: se la persona esiste già nella tabella popolazione e la data di fine a null, allora fail
        if ($this->categorie->count() > 0) {
            throw new Exception("Impossibile inserire `{$this->nominativo}` come prima volta nella comunita. Risulta essere già stata inserita.");
        }
        $interna = Categoria::perNome("interno");

        $persona_id = $this->id;
        DB::connection('db_nomadelfia')->beginTransaction();

        try {
            $conn = DB::connection('db_nomadelfia');

            // @deprecated: inserisce la categoria come persona interna. Usare la tabella popolazione
            $conn->insert(
                "INSERT INTO persone_categorie (persona_id, categoria_id, data_inizio, stato, created_at, updated_at) VALUES (?, ?, ?, 1, NOW(), NOW())",
                [$persona_id, $interna->id, $data]
            );

//            $conn->insert(
//                "INSERT INTO popolazione (persona_id, data_entrata, created_at, updated_at) VALUES (?, ?, NOW(), NOW())",
//                [$persona_id, $data]
//            );

            // inserisce la posizione in nomadelfia della persona
            $conn->insert(
                "INSERT INTO persone_posizioni (persona_id, posizione_id, data_inizio, stato) VALUES (?, ?, ?,'1')",
                [$persona_id, $posizione_id, $posizione_data]
            );

            // inserisce la persona nel gruppo familiare
            $conn->insert(
                "INSERT INTO gruppi_persone (gruppo_famigliare_id, persona_id, data_entrata_gruppo, stato) VALUES (?, ?, ?, '1')",
                [$gruppo_id, $persona_id, $gruppo_data]
            );

            if ($stato_id) {
                // inserisce lo stato familiare
                $conn->insert(
                    "INSERT INTO persone_stati (persona_id, stato_id, data_inizio, stato) VALUES (?, ?, ?,'1')",
                    [$persona_id, $stato_id, $stato_data]
                );
            }

            // inserisce la persona nella famiglia con una posizione
            if ($famiglia_id) {
                $conn->insert(
                    "INSERT INTO famiglie_persone (famiglia_id, persona_id, data_entrata, posizione_famiglia, stato) VALUES (?, ?, ?, ?, '1')",
                    [$famiglia_id, $persona_id, $famiglia_data, $famiglia_posizione]
                );
            }
            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            dd($e);
        }
    }

    public function setDataEntrataNomadelfia($data_entrata)
    {
        $int = Categoria::perNome("interno");

        $cat = $this->categoriaAttuale();
        if ($cat->isPersonaInterna()) {
            $data = $data_entrata ? $data_entrata : $this->data_nascita;
            return $this->categorie()->updateExistingPivot($int->id, ['data_inizio' => $data]);
        }
        throw new Exception("Error. La persona non ha una person ainterna");

    }

    public function getDataEntrataNomadelfia()
    {
        $int = Categoria::perNome("interno");
        $categorie = $this->categorie()->where('nome', $int->nome)->withPivot('stato', 'data_inizio',
            'data_fine')->orderby('data_inizio', 'desc');
        if ($categorie->count() > 0) {
            return $categorie->first()->pivot->data_inizio;
        }
        return null;
    }

    public function getDataUscitaNomadelfia()
    {
        $esterno = Categoria::perNome("esterno");
        $categorie = $this->categorie()->where('nome', $esterno->nome)->withPivot('stato', 'data_inizio',
            'data_fine')->orderby('data_inizio', 'desc');
        if ($categorie->count() > 0) {
            return $categorie->first()->pivot->data_inizio;
        }
        return null;
    }

    public function getDataDecesso()
    {
        return $this->data_decesso;
    }

    public function isPersonaInterna()
    {
        $categorie = $this->categorie()->wherePivot('stato', '1')->get();
        $isInterna = false;
        foreach ($categorie as $categoria) {
            if ($categoria->isPersonaInterna()) {
                $isInterna = true;
            }
        }
        return $isInterna;
    }

    /*
    * Return True if the person is dead, false otherwise
    */
    public function isDeceduto()
    {
        return $this->data_decesso != null;
    }

    public function deceduto($data_decesso)
    {
        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $this->uscita($data_decesso);

            $conn = DB::connection('db_nomadelfia');
            // aggiorna la data di decesso
            $conn->update(
                "UPDATE persone SET data_decesso = ?, stato = '0', updated_at = NOW() WHERE id = ?",
                [$data_decesso, $this->id]
            );

            // aggiorna lo stato familiare  con la data di decesso
            $conn->insert(
                "UPDATE persone_stati SET data_fine = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$data_decesso, $this->id]
            );

            // aggiorna la data di uscita dalla famiglia con la data di decesso
            $conn->insert(
                "UPDATE famiglie_persone SET data_uscita = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$data_decesso, $this->id]
            );

            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }

    /*
    * Fa uscire  una persona da Nomadelfia aggiornando tutte le posizioni attuali con la data di uscita.
    * Se disable_from_family è True e se è un minorenne, la persona viene anche messa fuori dal nucleo familiare.
    *
    * @param date $name
    * @param bool $disable_from_family
    */
    public function uscita($data_uscita, $disable_from_family = true)
    {
        $persona_id = $this->id;
        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $conn = DB::connection('db_nomadelfia');

            // disabilità la persona
            $conn->update("UPDATE persone SET stato = '0', updated_at = NOW() WHERE id = ? AND stato = '1'",
                [$persona_id]);

            // aggiunge la categoria persona esterna
            $conn->insert(
                "INSERT INTO persone_categorie (persona_id, categoria_id, data_inizio, stato, created_at, updated_at) VALUES (?, 4, ?, 1, NOW(), NOW())",
                [$persona_id, $data_uscita]
            );

            // aggiorna la categorie attive con la data di uscita
            $conn->update(
                "UPDATE persone_categorie SET data_fine = ?, stato = '0', updated_at = NOW() WHERE persona_id = ? AND stato = '1'",
                [$data_uscita, $persona_id]
            );

            // conclude la posizione in nomadelfia della persona con la data di uscita
            $conn->insert(
                "UPDATE persone_posizioni  SET data_fine = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$data_uscita, $persona_id]
            );

            // conclude la persona nel gruppo familiare con la data di uscita
            $conn->insert(
                "UPDATE gruppi_persone SET data_uscita_gruppo = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$data_uscita, $persona_id]
            );

            // conclude le aziende dove lavora con la data di uscita
            $conn->update(
                "UPDATE aziende_persone SET data_fine_azienda = ?, stato = '0' WHERE persona_id = ? AND stato = 'Attivo'",
                [$data_uscita, $persona_id]
            );

            if (!$this->isMaggiorenne() && $disable_from_family) {
                // toglie la persona dal nucleo familiare
                $conn->insert(
                    "UPDATE famiglie_persone  SET data_uscita = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                    [$data_uscita, $persona_id]
                );
            }

            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }

    /**
     * Ritorna le posizioni assegnabili ad una persona.
     * @return Collection Posizione
     * @author Davide Neri
     **/
    public function categoriePossibili()
    {
        $categoria = self::categoriaAttuale();
        $categorie = Categoria::all();
        if ($categoria != null) {
            $categorie = $categorie->except([$categoria->id]);
            // if($categoria->is(Posizione::findByName("EFFETTIVO")))
            //   return $categorie->except([Posizione::findByName("FIGLIO")->id]);
            // if($categoria->is(Posizione::findByName("POSTULANTE")))
            //   return $categorie->except([Posizione::findByName("FIGLIO")->id]);
            // if($categoria->is(Posizione::findByName("OSPITE")))
            //   return $categorie->except([Posizione::findByName("EFFETTIVO")->id]);
            // if($categoria->is(Posizione::findByName("FIGLIO")))
            //   return $categorie->except([Posizione::findByName("EFFETTIVO")->id]);
            return $categorie;
        } else {
            return $categorie;
        }
    }

    // STATO
    public function stati()
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

    public function statiStorico()
    {
        return $this->stati()->wherePivot('stato', '0')
            ->orderby('data_fine', 'desc');
    }

    /*
    * Assegna un nuovo stato alla persona.
    * Se la persona ha uno stato attuale viene concluso con la data di inizio del nuovo stato.
    */
    public function assegnaStato($stato, $data_inizio, $attuale_data_fine = null)
    {
        if (is_string($stato)) {
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
            throw new Exception("Bad Argument. Stato must be an id or a model.");
        }
    }


    // FAMIGLIA
    public function famiglie()
    {
        return $this->belongsToMany(Famiglia::class, 'famiglie_persone', 'persona_id', 'famiglia_id')
            ->withPivot("stato");
    }

    public function famigliaAttuale()
    {
        $famiglia = $this->famiglie()
            ->wherePivot('stato', '1')
            ->withPivot('posizione_famiglia', 'data_entrata', "data_uscita")
            ->get();
        if ($famiglia->count() == 1) {
            return $famiglia[0];
        } elseif ($famiglia->count() == 0) {
            return null;
        } else {
            throw PersonaHasMultipleFamigliaAttuale::named($this->nominativo);
        }
    }

    public function famiglieStorico()
    {
        return $this->famiglie()
            ->wherePivot('stato', '0')
            ->withPivot('posizione_famiglia', 'data_entrata', "data_uscita");
    }

    // Crea una famiglia e aggiunge la persona come componente
    // Se la data_entrata è null (default) viene settata uguale alla data di creazione della famiglia
    public function createAndAssignFamiglia($persona_id, $posizione, $nome, $data_creazione, $data_entrata = null)
    {
        try {
            DB::transaction(function () use (&$persona_id, &$posizione, &$nome, &$data_creazione, &$data_entrata) {
                $famiglia = Famiglia::create(['nome_famiglia' => $nome, 'data_creazione' => $data_creazione]);

                $famiglia->componenti()->attach($persona_id, [
                    'stato' => '1',
                    'posizione_famiglia' => $posizione,
                    'data_entrata' => ($data_entrata ? $data_entrata : $data_creazione),
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
     *
     * @param Famiglia
     *
     * @return $this
     */
    public function spostaNellaFamiglia($famiglia, $data_entrata, $posizione, $data_uscita = null)
    {
        if ($famiglia->single()) {
            throw  SpostaNellaFamigliaError::create($this->nominativo, $famiglia->nome_famiglia,
                "La famiglia single non può avere più di un componente");
        }
        $attuale = $this->famigliaAttuale();
        try {
            if (!$attuale) {
                $this->famiglie()->attach($famiglia->id,
                    ['stato' => '1', 'posizione_famiglia' => $posizione, 'data_entrata' => $data_entrata]);
            } else {
                // TODO; check se la persona può essere asseganta alla nuova famiglia
                DB::transaction(function () use (&$attuale, &$famiglia, &$data_uscita, &$data_entrata, &$posizione) {
                    DB::connection('db_nomadelfia')->update(
                        DB::raw("UPDATE famiglie_persone
                        SET  data_uscita = :uscita, stato = '0'
                        WHERE persona_id  = :persona AND famiglia_id = :famiglia "),
                        //  AND data_entrata = :entrata"), # TODO: mettere nella chiave primaria la data entrata ?
                        array(
                            "persona" => $this->id,
                            'famiglia' => $attuale->id,
                            "uscita" => ($data_uscita ? $data_uscita : $data_entrata)
                        )
                    );

                    $this->famiglie()->attach($famiglia->id,
                        ['stato' => '1', 'posizione_famiglia' => $posizione, 'data_entrata' => $data_entrata]);
                });
            }
        } catch (\Exception $e) {
            throw SpostaNellaFamigliaError::create($this->nominativo, $famiglia->nome_famiglia, str(e));
        }
        return $this;
    }


    /**
     * Ritorna la posizione di una persona in una famiglia
     * @param String $posizione
     * @return boolean
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
     * @return boolean
     * @author Davide Neri
     **/
    public function isMaggiorenne()
    {
        return Carbon::now()->diffInYears(Carbon::parse($this->data_nascita)) > 18;
    }


    /**
     * Ritorna vero se la persona è single altrimenti ritorna falso.
     * @return boolean
     * @author Davide Neri
     **/
    public function isSingle()
    {
        return $this->famigliaPosizione("SINGLE");
    }

    /**
     * Ritorna vero se una persona è il capo famiglia altrimenti ritorna falso.
     * @return boolean
     * @author Davide Neri
     **/
    public function isCapoFamiglia()
    {
        return $this->famigliaPosizione("CAPO FAMIGLIA");
    }

    /**
     * Ritorna vero se una persona è la moglie altrimenti ritorna falso.
     * @return boolean
     * @author Davide Neri
     **/
    public function isMoglie()
    {
        return $this->famigliaPosizione("MOGLIE");
    }

    /**
     * Ritorna vero se una persona è un figlioaccolto altrimenti ritorna falso.
     * @return boolean
     * @author Davide Neri
     **/
    public function isFiglio()
    {
        return $this->isFiglioNato() or $this->isFiglioAccolto();
    }

    /**
     * Ritorna vero se una persona è un figlio nato altrimenti ritorna falso.
     * @return boolean
     * @author Davide Neri
     **/
    public function isFiglioNato()
    {
        return $this->famigliaPosizione("FIGLIO NATO");
    }

    /**
     * Ritorna vero se una persona è un figlioaccolto altrimenti ritorna falso.
     * @return boolean
     * @author Davide Neri
     **/
    public function isFiglioAccolto()
    {
        return $this->famigliaPosizione("FIGLIO ACCOLTO");
    }

    // POSIZIONE
    public function posizioni()
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

    public function posizioniStorico()
    {
        return $this->posizioni()->wherePivot('stato', '0');
    }

    public function assegnaPosizione($posizione, $data_inizio, $attuale_data_fine)
    {
        if (is_string($posizione)) {
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
            throw new Exception("Bad Argument. Stato must be an id or a model.");
        }
    }

    public function modificaDataInizioPosizione($posizione_id, $currentDatain, $newDataIn)
    {
        $res = DB::connection('db_nomadelfia')->update(
            DB::raw("UPDATE persone_posizioni
               SET  data_inizio = :new
               WHERE posizione_id  = :posizone AND persona_id = :persona AND data_inizio = :current"),
            array("posizone" => $posizione_id, 'persona' => $this->id, "current" => $currentDatain, "new" => $newDataIn)
        );
        return $res;
    }

    public function concludiPosizione($posizione_id, $datain, $datafine)
    {
        $res = DB::connection('db_nomadelfia')->update(
            DB::raw("UPDATE persone_posizioni
               SET stato = '0', data_fine = :dataout
               WHERE posizione_id  = :posizone AND persona_id = :persona AND data_inizio = :datain"),
            array("posizone" => $posizione_id, 'persona' => $this->id, "datain" => $datain, "dataout" => $datafine)
        );
        return $res;
    }


    /**
     * Ritorna le posizioni assegnabili ad una persona.
     * @return Collection Posizione
     * @author Davide Neri
     **/
    public function posizioniPossibili()
    {
        $pos = self::posizioneAttuale();
        $posizioni = Posizione::all();
        if ($pos != null && $pos->count() == 1) {
            $pos = $pos->first();
            $posizioni = $posizioni->except([$pos->id]);
            if ($pos->is(Posizione::find("EFFE"))) {
                return $posizioni->except([Posizione::find("FIGL")->id]);
            }
            if ($pos->is(Posizione::find("POST"))) {
                return $posizioni->except([Posizione::find("FIGL")->id]);
            }
            if ($pos->is(Posizione::find("OSPP"))) {
                return $posizioni->except([Posizione::find("EFFE")->id]);
            }
            if ($pos->is(Posizione::find("FIGL"))) {
                return $posizioni->except([Posizione::find("EFFE")->id]);
            }
            return $posizioni;
        } else {
            return $posizioni;
        }
    }

    //***************************************************************************
    //                         Esercizi Spirituali
    //***************************************************************************

    public function eserciziSpirituali()
    {
        return $this->belongsToMany(EserciziSpirituali::class, 'persone_esercizi', 'persona_id', 'esercizi_id');
    }


    //INCARICHI
    public function incarichiAttuali()
    {
        return $this->belongsToMany(Incarico::class, 'organi_constituzionali_persone', 'persona_id',
            'organo_constituzionale_id')
            ->wherePivot('stato', '1');
    }

    public function incarichiStorici()
    {
        return $this->belongsToMany(Incarico::class, 'organi_constituzionali_persone', 'persona_id',
            'organo_constituzionale_id')
            ->wherePivot('stato', '0');
    }

    /**
     * Sposta una persona e la sua famiglia dal gruppo familiare attuale in un nuovo gruppo familiare.
     *
     * @param int|null $gruppoFamiliareAttuale
     * @param date $dataUscitaGruppoFamiliareAttuale
     * @param int $gruppoFamiliareNuovo
     * @param date $dataEntrataGruppo
     *
     */
    public function cambiaGruppoFamiliare(
        $gruppoFamiliareAttuale,
        $dataUscitaGruppoFamiliareAttuale,
        $gruppoFamiliareNuovo,
        $dataEntrataGruppo
    ) {
        if ($this->isCapoFamiglia() or $this->isSingle()) {
            $this->famigliaAttuale()->assegnaFamigliaANuovoGruppoFamiliare($gruppoFamiliareAttuale,
                $dataUscitaGruppoFamiliareAttuale, $gruppoFamiliareNuovo, $dataEntrataGruppo);
        }
    }

    public function assegnaPersonaANuovoGruppoFamiliare(
        $gruppoFamiliareAttuale,
        $dataUscitaGruppoFamiliareAttuale = null,
        $gruppoFamiliareNuovo,
        $dataEntrataGruppo = null
    ) {
        try {
            $this->gruppifamiliari()->updateExistingPivot($gruppoFamiliareAttuale,
                ['stato' => '0', 'data_uscita_gruppo' => $dataUscitaGruppoFamiliareAttuale]);
            $this->gruppifamiliari()->attach($gruppoFamiliareNuovo,
                ['stato' => '1', 'data_entrata_gruppo' => $dataEntrataGruppo]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
