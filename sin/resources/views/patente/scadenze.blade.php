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
        <h5 class="card-title">In scadenza entro {{config('patente.scadenze.patenti.inscadenza')}} gg ({{$patenti->count()}})</h5>
              <ul>
              @foreach($patenti as $patente)
                <li>
                  <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                  <span class="badge badge-warning"> {{ $patente->data_scadenza_patente}}
                    ({{Carbon::now('America/Vancouver')->diffInDays(Carbon::parse($patente->data_scadenza_patente))}}gg)
                  </span>
                </li>
              @endforeach
              </ul>
        <h5 class="card-title">Scadute da {{config('patente.scadenze.patenti.scadute')}} gg ({{$patentiScadute->count()}})</h5>
           <ul>
              @foreach($patentiScadute as $patente)
                <li>
                  <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                  <span class="badge badge-danger"> {{ $patente->data_scadenza_patente}}
                  ({{Carbon::now('America/Vancouver')->diffInDays(Carbon::parse($patente->data_scadenza_patente))}} gg)
                  </span>
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
        <h5 class="card-title">In scadenza entro {{config('patente.scadenze.commissione.inscadenza')}} gg ({{$patentiCommissione->count()}})</h5>
          <ul>
            @foreach($patentiCommissione as $patente)
              <li>
               <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
               <span class="badge badge-warning"> {{ $patente->data_scadenza_patente}}
               ({{Carbon::now('America/Vancouver')->diffInDays(Carbon::parse($patente->data_scadenza_patente))}} gg)
               </span>
            @endforeach
            </ul>
          <h5 class="card-title">Scadute da {{config('patente.scadenze.patenti.scadute')}} gg ({{$patentiCommisioneScadute->count()}})</h5>
           <ul>
              @foreach($patentiCommisioneScadute as $patente)
                <li>
                  <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                  <span class="badge badge-danger"> {{ $patente->data_scadenza_patente}}
                  ({{Carbon::now('America/Vancouver')->diffInDays(Carbon::parse($patente->data_scadenza_patente))}} gg)
                  </span>
                </li>
              @endforeach
            </ul>
        </div>
     </div>

     <div class="card">
      <div class="card-header"> C.Q.C</div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
                 <h5 class="card-title">C.Q.C Persone </h5>
                 <h6 class="card-subtitle">
                   In Scadenza entro  {{config('patente.scadenze.cqc.inscadenza')}} gg ({{$patentiCQCPersone->count()}})
                </h6>
                 <ul>
                   @foreach($patentiCQCPersone as $patente)
                    <li>
                      <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                      <span class="badge badge-warning"> {{ $patente->pivot->data_scadenza}}
                      ({{Carbon::now('America/Vancouver')->diffInDays(Carbon::parse($patente->pivot->data_scadenza))}}gg)
                      </span>
                    </li>
                   @endforeach
                </ul>
                
                <h6 class="card-subtitle">
                   Scadute da  {{config('patente.scadenze.cqc.scadute')}} gg ({{$patentiCQCPersoneScadute->count()}})
                </h6>
                <ul>
                  @foreach($patentiCQCPersoneScadute as $patente)
                    <li>
                      <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                      <span class="badge badge-danger"> {{ $patente->pivot->data_scadenza}}
                      ({{Carbon::now('America/Vancouver')->diffInDays(Carbon::parse($patente->pivot->data_scadenza))}}gg)
                      </span>
                    </li>
                  @endforeach
                </ul>
            </div>
            <div class="col-md-6">
            <h5 class="card-title">C.Q.C Merci </h5>
              <h6 class="card-subtitle">
                  In scadenza entro  {{config('patente.scadenze.cqc.inscadenza')}} gg ({{$patentiCQCMerci->count()}})
                </h6>
                 <ul>
                   @foreach($patentiCQCMerci as $patente)
                    <li>
                      <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                      <span class="badge badge-warning"> {{ $patente->pivot->data_scadenza}}
                        ({{Carbon::now('America/Vancouver')->diffInDays(Carbon::parse($patente->pivot->data_scadenza))}}gg)
                      </span>
                    </li>
                   @endforeach
                </ul>
             
              <h6 class="card-title">
                  Scadute da  {{config('patente.scadenze.cqc.scadute')}} gg ({{$patentiCQCMerciScadute->count()}})
                </h6>
                <ul>
                  @foreach($patentiCQCMerciScadute as $patente)
                    <li>
                      <a href="{{route('patente.modifica',['id'=>$patente->numero_patente])}}">{{ $patente->persona->nominativo }} </a>
                      <span class="badge badge-danger"> {{ $patente->pivot->data_scadenza}}
                      ({{Carbon::now('America/Vancouver')->diffInDays(Carbon::parse( $patente->pivot->data_scadenza))}}gg)
                      </span>
                    </li>
                  @endforeach
                </ul>


            </div>
          </div>
        </div>
      </div> <!--  end card C.Q.C  -->
    </div><!--  end card deck  -->
  </div>  <!--end col -->

</div>

@endsection