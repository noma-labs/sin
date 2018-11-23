@extends('patente.index')

@section('archivio')
<sin-header title="Scadenza patenti">  Numero totale patenti: {{App\Patente\Models\Patente::count()}} </sin-header>

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

  <table class="mt-2 table table-hover table-bordered table-sm"  style="table-layout:auto;overflow-x:scroll;">
    <thead class="thead-inverse">
        <tr>
            <th  style="width: 10%"> Nominativo </th>
            <th style="width: 10%"> {{ App\Traits\SortableTrait::link_to_sorting_action('numero_patente',"Numero Patente") }}</th>
            <th style="width: 10%"> {{ App\Traits\SortableTrait::link_to_sorting_action('numero_patente',"Data Scadenza") }} </th>
            <th style="width: 20%"> Categorie </th>
            <th style="width: 20%"> C.Q.C </th>
            <th style="width: 10%"> Operazioni </th>
        </tr>
    </thead>
    <tbody>
        @foreach($patentiAll as $patente)
          <tr hoverable>
              <td> {{$patente->persona->nominativo}}
              <span class="badge badge-warning">{{$patente->stato}}</span>
              </td>
              <td> {{$patente->numero_patente}}</td>
              <td> {{$patente->data_scadenza_patente}}</td>
              <td >{{$patente->categorieAsString()}} </td>
              <td> {{$patente->cqcAsString()}}</td>
              <td>
                <div class='btn-group' role='group' aria-label="Basic example">
                  <a class="btn btn-warning" href="{{ route('patente.modifica', $patente->numero_patente) }}">Modifica</a>
                  <!-- <a class="btn btn-danger" href="{{ route('patente.elimina', $patente->numero_patente) }}" data-toggle="modal" data-target="#eliminaModal">Elimina</a> -->
                </div>
              </td>
          </tr>
          @endforeach
      </tbody>

      <tbody>   
          @foreach($cqcAll as $patente)
          <tr hoverable>
              <td> {{$patente->persona->nominativo}}
              <span class="badge badge-warning">{{$patente->stato}}</span>
              </td>
              <td> {{$patente->numero_patente}}</td>
              <td> {{$patente->data_scadenza_patente}}</td>
              <td >{{$patente->categorieAsString()}} </td>
              <td> {{$patente->cqcAsString()}}</td>
              <td>
                <div class='btn-group' role='group' aria-label="Basic example">
                  <a class="btn btn-warning" href="{{ route('patente.modifica', $patente->numero_patente) }}">Modifica</a>
                  <!-- <a class="btn btn-danger" href="{{ route('patente.elimina', $patente->numero_patente) }}" data-toggle="modal" data-target="#eliminaModal">Elimina</a> -->
                </div>
              </td>
          </tr>
          @endforeach
    </tbody>
  </table>

@endsection