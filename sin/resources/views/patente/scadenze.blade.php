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
        <h5 class="card-title">Scadute ({{$patentiScadute->count()}})</h5>
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
          <h5 class="card-title">Scadute ({{$patentiCommisioneScadute->count()}})</h5>
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
                   Scadute  ({{$patentiCQCPersoneScadute->count()}})
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
                  Scadute ({{$patentiCQCMerciScadute->count()}})
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
            <th  style="width: 20%"> Nome Cognome </th>
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
              <td> 
              @isset($patente->persona->datipersonali->nome)
                 {{ $patente->persona->datipersonali->nome}}
              @endisset
              @isset($patente->persona->datipersonali->cognome)
                {{$patente->persona->datipersonali->cognome}}
              @endisset
                @if($patente->stato == 'commissione')
                  <span class="badge badge-warning">C.</span>
                @endif
                @isset($patente->note)
                <span class="badge badge-success">N.</span>
                @endisset
              </td>
              <td> {{$patente->numero_patente}}</td>
              <td> {{$patente->data_scadenza_patente}}</td>
              <td >{{$patente->categorieAsString()}} </td>
              <td> {{$patente->cqcAsString()}}</td>
              <td>
                <div class='btn-group' role='group' aria-label="Basic example">
                @can('patente.modifica')
                  <a class="btn btn-warning" href="{{ route('patente.modifica', $patente->numero_patente) }}">Modifica</a>
                @endcan
                @can('patente.elimina')
                  <a class="btn btn-danger" href="{{ route('patente.elimina', $patente->numero_patente) }}" >Elimina</a> 
                @endcan
                  <!-- <a class="btn btn-danger" href="{{ route('patente.elimina', $patente->numero_patente) }}" data-toggle="modal" data-target="#eliminaModal">Elimina</a> -->
                </div>
              </td>
          </tr>
          @endforeach
      </tbody>
    </tbody>
  </table>
  <div class="row">
       <div class="col-md-6 offset-md-4">
         {{ $patentiAll->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
      </div>
    </div>

  
<!-- Modal -->
<div class="modal fade" id="eliminaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Eliminazione patente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>Vuoi davvero elimare la patente ?</p>
     </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
        <a class="btn btn-danger" href="{{ route('patente.elimina', $patente->numero_patente) }}" >Elimina</a>  
      </div>
    </div>
  </div>
</div>

@endsection