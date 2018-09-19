<div class="row">
  <div class="col-md-3">
    <h2>Postulanti {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->count()}}</h2>
    <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->uomini()->count()}}</h4>
      @foreach(App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->uomini()->get() as $uomo)
      <div>{{$uomo->nominativo}}
      </div>
      @endforeach
      <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->donne()->count()}}</h4>
      @foreach(App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->donne()->get() as $donna)
      <div>{{$donna->nominativo}}
      </div>
      @endforeach
  </div>
  <div class="col-md-3">
    <h2>Ospiti {{App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->count()}}</h2>
    <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->uomini()->count()}}</h4>
      @foreach(App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->uomini()->get() as $uomo)
      <div>{{$uomo->nominativo}}
      </div>
      @endforeach
      <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->donne()->count()}}</h4>
      @foreach(App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->donne()->get() as $donna)
      <div>{{$donna->nominativo}}
      </div>
      @endforeach
  </div>
  <div class="col-md-3">
    <h2>Figli 18...21 {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->count()}}</h2>
    <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->uomini()->count()}}</h4>
    @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->uomini()->get() as $uomo)
      <div>{{$uomo->nominativo}}
      </div>
      @endforeach
      <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->donne()->count()}}</h4>
    @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->donne()->get() as $donna)
      <div>{{$donna->nominativo}}
      </div>
      @endforeach
  </div>
  <div class="col-md-3">
    <h2>Figli>21    {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->count()}}</h2>
    <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->uomini()->count()}}</h4>
    @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->uomini()->get() as $uomo)
      <div>{{$uomo->nominativo}}
      </div>
      @endforeach
      <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->donne()->count()}}</h4>
    @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->donne()->get() as $donna)
      <div>
        {{$donna->nominativo}}
      </div>
      @endforeach
  </div>
</div>
