@extends('patente.index')

@section('archivio')
<sin-header title="Scadenza patenti"> </sin-header>

<div class="row">
  <div class="col-md-4">
    <div class="card" style="width: 18rem;">
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
  </div>  <!--  end first col  -->
  <div class="col-md-4">
    <div class="card" style="width: 18rem;">
      <div class="card-header">
          C.Q.C in scadenza 
        </div>
        <div class="card-body">
          <h5 class="card-title">In scadenza ({{$patentiCQC->count()}})</h5>
          <ul>
            @foreach($patentiCQC as $patente)
              <li>
               <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
               <span class="badge badge-warning"> {{ $patente->data_scadenza_patente}}</span>
                </li>
            @endforeach
            </ul>
            <h5 class="card-title">Scadute ({{$patentiCQCScadute->count()}})</h5>
            <ul>
              @foreach($patentiCQCScadute as $patente)
                <li>
                  <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                  <span class="badge badge-danger"> {{ $patente->data_scadenza_patente}}</span>
                </li>
              @endforeach
            </ul>
        </div>
      </div>
  </div> <!--  end second col  -->
  <div class="col-md-4">
    <div class="card" style="width: 18rem;">
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
  </div> <!--  end third col  -->
</div>

@endsection