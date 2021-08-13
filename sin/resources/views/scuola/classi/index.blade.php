@extends('scuola.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Classi'])


@foreach ($classi->chunk(3) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $classe)
          <div class="col-md-4">
            <div id="accordion">
                <div class="card">
                  <div class="card-header" id="heading{{$classe->id}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$classe->id}}" aria-expanded="true" aria-controls="collapse{{$classe->id}}">
                        {{ $classe->tipo->nome }}
                        <span class="badge badge-primary badge-pill">{{ $classe->alunni()->count() }}</span>
                      </button>
                    </h5>
                  </div>
                    
                  <div id="collapse{{$classe->id}}" class="collapse" aria-labelledby="heading{{$classe->id}}" data-parent="#accordion">
                    <div class="card-body">
                      <ul>
                      @foreach($classe->alunni as $alunno)
                        <li> {{$alunno->data_nascita}}@include('nomadelfia.templates.persona', ['persona'=>$alunno])</li>
                      @endforeach
                      </ul>
                      <div class="row">
                          <a class="btn btn-danger btn-block col-md-4 offset-md-2" type="button" href="{{ route('nomadelfia.incarichi.edit', $classe->id)}}">Modifica</a>
                      </div>            
                    </div>
                  </div>
                 </div>
                </div>  <!-- end card -->
              </div>
        @endforeach
    </div>
@endforeach
@endsection
