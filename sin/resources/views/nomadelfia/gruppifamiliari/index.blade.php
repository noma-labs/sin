@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Gruppo Familiari'])

<div class="col-md-8 offset-md-2">
  <div id="accordion">
  @foreach($gruppifamiliari as $gruppo)
    <div class="card">
      <div class="card-header" id="headingOne">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#{{$gruppo->id}}" aria-expanded="false" aria-controls="collapseOne">
          <span class="font-weight-bold"> {{$gruppo->nome}}</span> ({{$gruppo->persone->count()}})
           Capogruppo: 
            @if ($gruppo->capogruppiAttuali->isNotEmpty())
            <span class="font-weight-bold">  {{$gruppo->capogruppiAttuali->first()->nominativo}}</span> 
            @else
            <span class="text-danger">Senza capogruppo</span> 
            @endif
          </button>
        </h5>
      </div>
      <div id="{{$gruppo->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">
          <!-- <span class="font-weight-bold">{{$gruppo->famiglie->count()}}</span> -->
         
          @foreach($gruppo->famiglie as $famiglia)
            Famiglia: <span>{{$famiglia->nome_famiglia}}
            <ul>
              @foreach($famiglia->componenti as $componente)
              <li>{{$componente->nominativo}}</li>
              @endforeach
            </ul>
          @endforeach
        
        </div>
      </div>
    </div>
  @endforeach
 </div>
</div>

<!--
<div class="table-responsive">
  <table class="table table-condensed">
    <table class='table table-bordered'>
      <thead class='thead-inverse'>
        <tr>
          <th>Gruppo</th>
          <th>Descrizione</th>
        </tr>
   </thead>
  <tbody>
  @forelse ($gruppifamiliari as $gruppo)
      <tr>
        <td width="10">{{ $gruppo->nome }}</td>
        <td width="10">{{$gruppo->descrizione_gruppo}}</td>
      </tr>
  @empty
      <div class="alert alert-danger">
          <strong>Nessun risultato ottenuto</strong>
      </div>
  @endforelse
</tbody>
</table>
</div>
-->

@endsection
