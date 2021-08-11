@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Gruppi Familiari'])

@foreach(collect($g)->chunk(3) as $chunk)
 <div class="row">
    @foreach ($chunk as $gruppo)
      <div class="col-md-4 my-1">
          <div id="accordion">
            <div class="card">
              <div class="card-header" id="heading{{$gruppo->id}}">
                <h5 class="mb-0">
                  <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$gruppo->id}}" aria-expanded="false" aria-controls="collapse{{$gruppo->id}}">
                      {{$gruppo->nome}} 
                  <span class="badge badge-primary badge-pill">
                  {{$gruppo->count}}
                  </span> 
                  </button>
                </h5>
              </div>
              <div id="collapse{{$gruppo->id}}" class="collapse" aria-labelledby="heading{{$gruppo->id}}" data-parent="#accordion">
                <div class="card-body">
                  
                  </ul>
                    <a class="btn btn-primary" href="{{ route('nomadelfia.gruppifamiliari.dettaglio', $gruppo->id)}}">Dettaglio</a>
                </div>    
              </div>
            </div>
          </div>
      </div>      
     @endforeach
 </div> 
@endforeach

@endsection
