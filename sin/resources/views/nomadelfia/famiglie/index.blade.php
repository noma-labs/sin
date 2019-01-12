@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Famiglie'])

<div class="row">
  <div class="col-md-6">
    <div id="accordion">
      <div class="card">
        <div class="card-header" id="headSingle">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#Single" aria-expanded="true" aria-controls="Single">
            Famiglie con capo Famiglia  <span class="badge badge-primary badge-pill">{{$capifamiglie->count()}}</span> 
            </button>
          </h5>
        </div>
        <div id="Single" class="collapse" aria-labelledby="headSingle" data-parent="#accordion">
          <div class="card-body">
              @foreach($capifamiglie->get() as $famiglia)
              <div>
                 <a href="{{route('nomadelifa.famiglia.dettaglio',['id'=>$famiglia->id])}}"> {{$famiglia->nome_famiglia}}</a>
              </div>
              @endforeach              
          </div>
        </div>
      </div> <!-- end famiglie capo famiglia card -->
    </div> <!-- end accordion -->
  </div>

  <div class="col-md-6">
    <div id="accordion">
      <div class="card">
      <div class="card-header" id="headCapoFamiglia">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#CapoFamiglia" aria-expanded="true" aria-controls="CapoFamiglia">
          Famiglie con Single   <span class="badge badge-primary badge-pill">{{$single->count()}}</span> 
          </button>
        </h5>
      </div>
      <div id="CapoFamiglia" class="collapse" aria-labelledby="headCapoFamiglia" data-parent="#accordion">
        <div class="card-body">
            @foreach($single->get() as $famiglia)
            <div>{{$famiglia->nome_famiglia}}</div>
            @endforeach              
        </div>
      </div>
    </div> <!-- end single famiglia card -->
    </div> <!-- end accordion -->
  </div>
</div>

@endsection
