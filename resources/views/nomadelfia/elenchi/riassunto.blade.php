<p class="fw-bold">Popolazione Nomadelfia</p>
<p>{{ Carbon::now()->toDateString() }}</p>

<div>
    Maggiorenni:
    {{ App\Nomadelfia\Models\Persona::maggiorenni()->attivo()->count() }}
</div>
<div>
    Uomini:
    {{ App\Nomadelfia\Models\Persona::maggiorenni()->uomini()->attivo()->count() }}
</div>
<div>
    Donne:
    {{ App\Nomadelfia\Models\Persona::maggiorenni()->donne()->attivo()->count() }}
</div>
<hr />
<div>
    Effettivi:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("effettivo")->persone()->count() }}
</div>
<div>
    Uomini:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("effettivo")->persone()->uomini()->count() }}
</div>
<div>
    Donne:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("effettivo")->persone()->donne()->count() }}
</div>
<hr />
<div>
    Postulante:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("postulante")->persone()->count() }}
</div>
<div>
    Uomini:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("postulante")->persone()->uomini()->count() }}
</div>
<div>
    Donne:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("postulante")->persone()->donne()->count() }}
</div>
<hr />
<div>
    Sacerdoti:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;::perNome("sacerdote")->persone()->attivo()->count() }}
</div>
<div>
    Mamme di vocazione:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;::perNome("mammavocazione")->persone()->attivo()->count() }}
</div>
<hr />
<div>
    Figli Maggiorenni:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("figlio")->persone()->maggiorenni()->attivo()->count() }}
</div>
<div>
    Uomini:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("figlio")->persone()->maggiorenni()->uomini()->attivo()->count() }}
</div>
<div>
    Donne:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("figlio")->persone()->maggiorenni()->donne()->attivo()->count() }}
</div>
<hr />
<div>
    Figli Minorenni:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("figlio")->persone()->minorenni()->attivo()->count() }}
</div>
<div>
    Uomini:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("figlio")->persone()->minorenni()->uomini()->attivo()->count() }}
</div>
<div>
    Donne:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("figlio")->persone()->minorenni()->donne()->attivo()->count() }}
</div>
<div>
    Accolti:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("figlio")->persone()->minorenni()->donne()->attivo()->count() }}
</div>
<div>
    Nati da matrimoni:
    {{ Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;::perNome("figlio")->persone()->minorenni()->donne()->attivo()->count() }}
</div>
<hr />
<div>Famiglia</div>
