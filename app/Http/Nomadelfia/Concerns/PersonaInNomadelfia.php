<?php

declare(strict_types=1);

namespace App\Nomadelfia\Concerns;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use App\Nomadelfia\Exceptions\PersonaHasMultipleGroup;
use App\Nomadelfia\Exceptions\PersonaHasMultiplePosizioniAttuale;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Incarico\Models\Incarico;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Relations and behaviour of a Persona inside the Nomadelfia community: gruppi familiari, aziende, incarichi, cariche, stati, posizioni and popolazione.
 */
trait PersonaInNomadelfia
{
    /**
     * Returns the ids of the people currently living in Nomadelfia.
     *
     * @return Collection<array-key, mixed>
     */
    public static function idsInterni(): Collection
    {
        return self::queryIdsInterni()->pluck('persona_id');
    }

    /**
     * Query selecting the ids of the people currently living in Nomadelfia, for use as a subquery.
     */
    public static function queryIdsInterni(): Builder
    {
        return DB::connection('db_nomadelfia')
            ->table('popolazione')
            ->select('persona_id')
            ->whereNull('data_uscita');
    }

    public function gruppifamiliari(): BelongsToMany
    {
        return $this->belongsToMany(GruppoFamiliare::class, 'gruppi_persone', 'persona_id', 'gruppo_famigliare_id')
            ->withPivot('data_entrata_gruppo', 'data_uscita_gruppo', 'stato');
    }

    public function gruppofamiliareAttuale()
    {
        $gruppo = $this->gruppifamiliari()->wherePivot('stato', '1')->get();
        if ($gruppo->count() === 1) {
            return $gruppo[0];
        }
        if ($gruppo->count() === 0) {
            return null;
        }
        throw PersonaHasMultipleGroup::named($this);
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
        if ($multiplied->count() > 0) {
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

    public function eserciziSpirituali(): BelongsToMany
    {
        return $this->belongsToMany(EserciziSpirituali::class, 'persone_esercizi', 'persona_id', 'esercizi_id');
    }

    /**
     * @return BelongsToMany<Stato, $this, Pivot, 'pivot'>
     */
    public function stati(): BelongsToMany
    {
        return $this->belongsToMany(Stato::class, 'persone_stati', 'persona_id', 'stato_id')
            ->withPivot('stato', 'data_inizio', 'data_fine');
    }

    /**
     * The stati the person has now. More than one can be active together, e.g. sacerdote and separato.
     *
     * @return BelongsToMany<Stato, $this, Pivot, 'pivot'>
     */
    public function statiAttivi(): BelongsToMany
    {
        return $this->stati()->wherePivot('stato', '1')->orderByPivot('data_inizio');
    }

    /**
     * The coniuge of the person: the moglie of a capofamiglia, or the capofamiglia of a moglie, even if deceased.
     * A capofamiglia without a moglie, like a mamma di vocazione with her figli accolti, has no coniuge.
     */
    public function coniuge(): ?Persona
    {
        $famiglia = $this->famigliaDaCapofamigliaOMoglie();

        return $famiglia ? $this->coniugeNellaFamiglia($famiglia) : null;
    }

    /**
     * The famiglia born from the marriage of the person, or null if the person is not married.
     */
    public function matrimonioAttuale(): ?Famiglia
    {
        $famiglia = $this->famigliaDaCapofamigliaOMoglie();

        return $famiglia && $this->coniugeNellaFamiglia($famiglia) ? $famiglia : null;
    }

    /**
     * The stato civile of the person, derived instead of stored:
     * separato (stato SEP), vedovo (the coniuge is deceased), sposato (from the creation of the famiglia),
     * vedovo or sposato with a coniuge outside Nomadelfia (stato VED or SPO, as the coniuge is not in the famiglia), otherwise celibe or nubile.
     *
     * @return array{nome: string, dal: Carbon}
     */
    public function statoCivile(): array
    {
        $maschio = $this->isMaschio();
        $separazione = $this->statiAttivi()->where('stati.stato', Stato::$mapNamesToDB['separato'])->value('persone_stati.data_inizio');
        if (is_string($separazione)) {
            return ['nome' => $maschio ? 'Separato' : 'Separata', 'dal' => \Illuminate\Support\Facades\Date::parse($separazione)];
        }

        $famiglia = $this->famigliaDaCapofamigliaOMoglie();
        $coniuge = $famiglia ? $this->coniugeNellaFamiglia($famiglia) : null;
        if ($coniuge?->data_decesso) {
            return ['nome' => $maschio ? 'Vedovo' : 'Vedova', 'dal' => \Illuminate\Support\Facades\Date::parse($coniuge->data_decesso)];
        }
        if ($coniuge) {
            return ['nome' => $maschio ? 'Sposato' : 'Sposata', 'dal' => \Illuminate\Support\Facades\Date::parse($famiglia->data_creazione)];
        }

        $statiConiugeEsterno = $this->statiAttivi()
            ->whereIn('stati.stato', [Stato::$mapNamesToDB['vedovoconiugeesterno'], Stato::$mapNamesToDB['sposatoconiugeesterno']])
            ->pluck('persone_stati.data_inizio', 'stati.stato');
        $vedovanza = $statiConiugeEsterno[Stato::$mapNamesToDB['vedovoconiugeesterno']] ?? null;
        if (is_string($vedovanza)) {
            return ['nome' => $maschio ? 'Vedovo' : 'Vedova', 'dal' => \Illuminate\Support\Facades\Date::parse($vedovanza)];
        }
        $matrimonio = $statiConiugeEsterno[Stato::$mapNamesToDB['sposatoconiugeesterno']] ?? null;
        if (is_string($matrimonio)) {
            return ['nome' => $maschio ? 'Sposato' : 'Sposata', 'dal' => \Illuminate\Support\Facades\Date::parse($matrimonio)];
        }

        return ['nome' => $maschio ? 'Celibe' : 'Nubile', 'dal' => \Illuminate\Support\Facades\Date::parse($this->data_nascita)];
    }

    public function statiStorico(): BelongsToMany
    {
        return $this->stati()->wherePivot('stato', '0')
            ->orderby('data_fine', 'desc');
    }

    /**
     * @return BelongsToMany<Posizione, $this, Pivot, 'pivot'>
     */
    public function posizioni(): BelongsToMany
    {
        return $this->belongsToMany(Posizione::class, 'persone_posizioni', 'persona_id', 'posizione_id')
            ->withPivot('stato', 'data_inizio', 'data_fine');
    }

    public function posizioneAttuale()
    {
        $posizione = $this->posizioni()->wherePivot('stato', '1')->get();
        if ($posizione->count() === 1) {
            return $posizione[0];
        }
        if ($posizione->count() === 0) {
            return null;
        }
        throw PersonaHasMultiplePosizioniAttuale::named($this->nominativo);
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

    public function getDataEntrataNomadelfia(): Carbon
    {
        $pop = PopolazioneNomadelfia::where('persona_id', $this->id)->orderBy('data_entrata', 'DESC')->get();
        if (count($pop) > 0) {
            return \Illuminate\Support\Facades\Date::parse($pop->first()->data_entrata);
        }

        throw new Exception("La persona $this->nominativo non risulta essere mai entrata");
    }

    public function getDataUscitaNomadelfia(): ?Carbon
    {

        $pop = PopolazioneNomadelfia::where('persona_id', $this->id)->orderBy('data_uscita', 'DESC')->whereNotNull('data_uscita');
        if ($pop->count() > 0) {
            return \Illuminate\Support\Facades\Date::parse($pop->first()->data_uscita);
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

    /**
     * Adds a stato to the person, keeping the other active stati: the stati are independent of each other.
     *
     * @throws Exception if the person already has the stato active
     */
    public function assegnaStato(int|string|Stato $stato, string|Carbon $data_inizio): void
    {
        if (! $stato instanceof Stato) {
            $stato = Stato::findOrFail($stato);
        }
        if ($this->statiAttivi()->where('stati.id', $stato->id)->exists()) {
            throw new Exception("La persona {$this->nominativo} ha già lo stato {$stato->nome}.");
        }
        $this->stati()->attach($stato->id, ['stato' => '1', 'data_inizio' => $data_inizio]);
    }

    public function assegnaPosizione(
        $posizione,
        Carbon $data_inizio,
        ?Carbon $attuale_data_fine = null
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
                        ['stato' => '0', 'data_fine' => ($attuale_data_fine ? $attuale_data_fine->toDateString() : $data_inizio->toDateString())]);
                }
                $this->posizioni()->attach($posizione->id, ['stato' => '1', 'data_inizio' => $data_inizio->toDateString()]);
                DB::connection('db_nomadelfia')->commit();
            } catch (Exception $e) {
                DB::connection('db_nomadelfia')->rollback();
                throw $e;
            }
        } else {
            throw new Exception('Bad Argument. Posizione must be an id or a model.');
        }
    }

    public function modificaDataInizioPosizione(
        $posizione_id,
        Carbon $currentDatain,
        Carbon $newDataIn
    ) {
        $expression = DB::raw('UPDATE persone_posizioni
               SET  data_inizio = :new
               WHERE posizione_id  = :posizone AND persona_id = :persona AND data_inizio = :current');

        return DB::connection('db_nomadelfia')->update(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['posizone' => $posizione_id, 'persona' => $this->id, 'current' => $currentDatain->toDateString(), 'new' => $newDataIn->toDateString()]
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
        if ($pos !== null && $pos->count() === 1) {
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
        }

        return $posizioni;

    }

    /**
     * Sposta una persona e la sua famiglia dal gruppo familiare attuale in un nuovo gruppo familiare.
     *
     * @param  int|null  $gruppoFamiliareAttuale
     * @param  Carbon  $dataUscitaGruppoFamiliareAttuale
     * @param  int  $gruppoFamiliareNuovo
     * @param  Carbon  $dataEntrataGruppo
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

    /**
     * The famiglia where the person is capofamiglia or moglie, preferring the one the person is still in:
     * a deceased person is no longer in the nucleo but stays married.
     */
    private function famigliaDaCapofamigliaOMoglie(): ?Famiglia
    {
        return $this->famiglie()
            ->wherePivotIn('posizione_famiglia', ['CAPO FAMIGLIA', 'MOGLIE'])
            ->orderByPivot('stato', 'desc')
            ->first();
    }

    /**
     * The other capofamiglia or moglie of the famiglia, also if no longer in the nucleo (e.g. deceased).
     */
    private function coniugeNellaFamiglia(Famiglia $famiglia): ?Persona
    {
        return $famiglia->componenti()
            ->wherePivotIn('posizione_famiglia', ['CAPO FAMIGLIA', 'MOGLIE'])
            ->where('persone.id', '!=', $this->id)
            ->first();
    }
}
