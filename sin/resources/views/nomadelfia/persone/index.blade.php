@extends('nomadelfia.index')

@section('navbar-link')
  <li class="nav-item">
      <a class="nav-link" href="{{ route('nomadelfia') }}">Nomadelfia</a>
  </li>
  <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Persone
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{route('persone.inserimento')}}">Inserisci Persona</a>
          <a class="dropdown-item" href="{{route('nomadelfia.autocomplete.persona')}}">Ricerca Persona</a>
        </div>
  </li>
@endsection

@section('archivio')
<div class="container">
 <div class="row">
  <div class="col-md-4">
    <div id="accordion">
        <div class="card">
        <div class="card-header" id="headingOne">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              Nomadelfi effettivi {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->presente()->count()}}
            </button>
          </h5>
        </div>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
          <div class="card-body">
           <div class="row">
            <div class="col-md-6"> 
              <h5>Uomini {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->presente()->uomini()->count()}}</h5>
                @foreach(App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->uomini()->get() as $uomo)
                  <div>
                  {{$uomo->nominativo}}
                  </div>
               @endforeach
            </div>
            <div class="col-md-6"> 
              <h5>Donne {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->presente()->donne()->count()}}</h5>
                @foreach(App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->donne()->get() as $donna)
                  <div>
                  {{$donna->nominativo}}
                  </div>
               @endforeach
            </div>
           </div>
          </div>
        </div>
      </div> <!-- end nomadelfi effettivi card -->
    </div> <!-- end accordion -->
  </div> <!-- end first col-md-4 -->
 <div class="col-md-4">
  <div id="accordion">
    <div class="card">
      <div class="card-header" id="headingOne">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapsePostulanti" aria-expanded="true" aria-controls="collapseOne">
            Postulanti {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->presente()->count()}}
          </button>
        </h5>
      </div>
      <div id="collapsePostulanti" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">
          <div class="row">
           <div class="col-md-6">
              <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->uomini()->count()}}</h4>
              @foreach(App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->uomini()->presente()->get() as $uomo)
                <div>
                {{$uomo->nominativo}}
                </div>
              @endforeach
           </div> <!-- end col uomini postulanti-->
           <div class="col-md-6">
              <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->donne()->count()}}</h4>
              @foreach(App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->donne()->get() as $donna)
                <div>
                {{$donna->nominativo}}
                </div>
                @endforeach
           </div><!-- end col donne postulanti-->
          </div>  <!-- end row inside postulante card -->
        </div>
      </div><!-- end card postulanto -->
    </div> 
  </div> <!-- end accordion second colum-->
 </div> <!-- end second col-md-4 -->
 
  <div class="col-md-4">
    <div id="accordion">
      <div class="card">
        <div class="card-header" id="headingOne">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseFigli" aria-expanded="true" aria-controls="collapseOne">
              Figli  {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->presente()->count()}}
            </button>
          </h5>
        </div>
        <div id="collapseFigli" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
          <div class="card-body">
          <div class="row">
              <div class="col-md-6">
               <h5> Minorenni {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->presente()->count()}}</h5>
                @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->presente()->get() as $figlio)
                <div>{{$figlio->nominativo}}
                </div>
                @endforeach
              </div> <!-- end col figli minorenni-->
              <div class="col-md-6">
               <h5> Maggiorenni {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->maggiorenni()->presente()->count() }} </h5>
                @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->maggiorenni()->presente()->get() as $figlio)
                <div>{{$figlio->nominativo}}
                </div>
                @endforeach
              </div> <!-- end col figli maggiorenni-->
            </div>  <!-- end row inside card-->
          </div>
        </div>
      </div> <!-- end card figli -->
    </div>  <!-- end accordion third row -->
  </div>  <!-- end third  col-md-4 -->
 
 <div> <!-- end row -->
</div> <!-- end container -->


@endsection

