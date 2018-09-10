<p class="font-weight-bold">Popolazione Nomadelfia</p> 
<p>{{Carbon::now()->toDateString()}}</p>

<div>Maggiorenni: {{App\Nomadelfia\Models\Persona::maggiorenni()->presente()->count()}}</div>
<div>Uomini: {{App\Nomadelfia\Models\Persona::maggiorenni()->uomini()->presente()->count()}}</div>
<div>Donne: {{App\Nomadelfia\Models\Persona::maggiorenni()->donne()->presente()->count()}}</div>
<hr>
<div>Effettivi: {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->count()}}</div>
<div>Uomini: {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->uomini()->count()}}</div>
<div>Donne: {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->donne()->count() }}</div>
<hr>
<div>Postulante: {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->count()}}</div>
<div>Uomini: {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->uomini()->count()}}</div>
<div>Donne: {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->donne()->count() }}</div>
<hr>
<div>Sacerdoti: {{App\Nomadelfia\Models\Stato::perNome("sacerdote")->persone()->presente()->count()}}</div>
<div>Mamme di vocazione: {{App\Nomadelfia\Models\Stato::perNome("mammavocazione")->persone()->presente()->count()}}</div>
<hr>
<div>Figli Maggiorenni: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->maggiorenni()->presente()->count()}}</div>
<div>Uomini: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->maggiorenni()->uomini()->presente()->count()}}</div>
<div>Donne: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->maggiorenni()->donne()->presente()->count()}}</div>
<hr>
<div>Figli Minorenni: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->presente()->count()}}</div>
<div>Uomini: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->uomini()->presente()->count()}}</div>
<div>Donne: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->donne()->presente()->count()}}</div>
<div>Accolti: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->donne()->presente()->count()}}</div>
<div>Nati da matrimoni: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->donne()->presente()->count()}}</div>
<hr>
<div>Famiglia</div>