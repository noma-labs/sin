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
            Famiglie Capo Famiglia  <span class="badge badge-primary badge-pill"></span> 
            </button>
          </h5>
        </div>
        <div id="Single" class="show" aria-labelledby="headSingle" data-parent="#accordion">
          <div class="card-body">
            <div class="row">
                <div class="col-md-6"> 
                  <h5>Uomini <span class="badge badge-primary badge-pill">{{$capifamiglieMaschio->count()}}</span></h5>
                    
                    @foreach($capifamiglieMaschio->get() as $uomo)
                      <div>
                          <a href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$uomo->id])}}"> {{$uomo->nome_famiglia}}</a>
                      </div>
                    @endforeach
                </div>
                <div class="col-md-6"> 
                  <h5>Donne  <span class="badge badge-primary badge-pill">{{$capifamiglieFemmina->count()}} </span></h5>
                    @foreach($capifamiglieFemmina->get() as $donna)
                      <div>
                        <a href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$donna->id])}}"> {{$donna->nome_famiglia}}</a>                                      
                      </div>
                  @endforeach
                </div>
              </div>


              <!-- @foreach($capifamiglieMaschio->get() as $famiglia)
              <div>
                 <a href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$famiglia->id])}}"> {{$famiglia->nome_famiglia}}</a>
              </div>
              @endforeach               -->
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
          Famiglie Single
        
          </button>
        </h5>
      </div>
      <div id="CapoFamiglia" class="show" aria-labelledby="headCapoFamiglia" data-parent="#accordion">
        <div class="card-body">
        <div class="row">
                <div class="col-md-6"> 
                  <h5>Uomini  <span class="badge badge-primary badge-pill">{{$singleMaschio->count()}}  </span> </h5>
                    
                    @foreach($singleMaschio->get() as $uomo)
                      <div>
                          <a href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$uomo->id])}}"> {{$uomo->nome_famiglia}}</a>
                      </div>
                    @endforeach
                </div>
                <div class="col-md-6"> 
                  <h5>Donne  <span class="badge badge-primary badge-pill">{{$singleFemmine->count()}}</span></h5>
                    @foreach($singleFemmine->get() as $donna)
                      <div>
                        <a href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$donna->id])}}"> {{$donna->nome_famiglia}}</a>                                      
                      </div>
                  @endforeach
                </div>
              </div>         
        </div>
      </div>
    </div> <!-- end single famiglia card -->
    </div> <!-- end accordion -->
  </div>
</div>

@endsection
