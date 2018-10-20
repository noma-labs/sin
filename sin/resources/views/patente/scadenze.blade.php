@extends('patente.index')

@section('archivio')
<sin-header title="Scadenza patenti"> </sin-header>

<div class="row">
   <div class="col align-self-center"> 
    <div class="card-deck">

    <div class="card">
      <div class="card-header">
        Patenti
      </div>
      <div class="card-body">
        <h5 class="card-title">In scadenza ({{$patenti->count()}})</h5>
              <ul>
              @foreach($patenti as $patente)
                <li>
                  <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                  <span class="badge badge-warning"> {{ $patente->data_scadenza_patente}}</span>
                </li>
              @endforeach
              </ul>
        <h5 class="card-title">Scadute ({{$patentiScadute->count()}})</h5>
           <ul>
              @foreach($patentiScadute as $patente)
                <li>
                  <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                  <span class="badge badge-danger"> {{ $patente->data_scadenza_patente}}</span>
                </li>
              @endforeach
            </ul>
      </div>
    </div>

    <div class="card">
         <div class="card-header">
        Patenti con commissione 
        </div>
        <div class="card-body">
        <h5 class="card-title">In scadenza ({{$patentiCommissione->count()}})</h5>
          <ul>
            @foreach($patentiCommissione as $patente)
              <li>
               <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
               <span class="badge badge-warning"> {{ $patente->data_scadenza_patente}}</span>
            @endforeach
            </ul>
          <h5 class="card-title">Scadute ({{$patentiCommisioneScadute->count()}})</h5>
           <ul>
              @foreach($patentiCommisioneScadute as $patente)
                <li>
                  <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                  <span class="badge badge-danger"> {{ $patente->data_scadenza_patente}}</span>
                </li>
              @endforeach
            </ul>
        </div>
     </div>

     <div class="card">
      <div class="card-header">
          C.Q.C in scadenza 
        </div>
        <div class="card-body">
          <h5 class="card-title">In scadenza ({{$patentiCQCPersone->count()}})</h5>
          <ul>
            @foreach($patentiCQCPersone as $patente)
              <li>
               <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
               <span class="badge badge-warning"> {{ $patente->CQCPersone->first()->pivot->data_scadenza}}</span>
                </li>
            @endforeach
            </ul>
            <h5 class="card-title">Scadute ({{$patentiCQCPersoneScadute->count()}})</h5>
            <ul>
              @foreach($patentiCQCPersoneScadute as $patente)
                <li>
                  <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                  <span class="badge badge-danger"> {{ $patente->CQCPersone->first()->pivot->data_scadenza}}</span>
                </li>
              @endforeach
            </ul>
        </div>
      </div> <!--  end card C.Q.C  -->
    </div><!--  end card deck  -->
  </div>  <!--end col -->

</div>

@endsection