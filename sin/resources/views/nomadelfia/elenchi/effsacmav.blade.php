
<div class="row">
    <div class="col-md-3">
      <h2>Effettivi Uomini {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->uomini()->count()}}</h2>
        @foreach(App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->uomini()->get() as $uomo)
        <div>{{$uomo->nominativo}}
        </div>
        @endforeach
    </div>
    <div class="col-md-3">
      <h2>Effettivi Donne {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->donne()->count()}}</h2>
        @foreach(App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->donne()->get() as $donna)
        <div>{{$donna->nominativo}}
        </div>
        @endforeach
    </div>
    <div class="col-md-3">
      <h2>Sacerdoti {{App\Nomadelfia\Models\Stato::perNome("sacerdote")->persone()->attivo()->count()}}</h2>
        @foreach(App\Nomadelfia\Models\Stato::perNome("sacerdote")->persone()->get() as $sac)
        <div>{{$sac->nominativo}}
        </div>
        @endforeach
    </div>
    <div class="col-md-3">
      <h2>Mamme vocazione {{App\Nomadelfia\Models\Stato::perNome("mammavocazione")->persone()->attivo()->count()}}</h2>
        @foreach(App\Nomadelfia\Models\Stato::perNome("mammavocazione")->persone()->get() as $mamma)
        <div>{{$mamma->nominativo}}
        </div>
        @endforeach
    </div>
</div>
