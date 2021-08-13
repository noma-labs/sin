@extends('scuola.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Classi'])


@foreach ($classi->chunk(3) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $incarico)
          <div class="col-md-4">
            <div id="accordion">
                <div class="card">
                  <div class="card-header" id="heading{{$incarico->id}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$incarico->id}}" aria-expanded="true" aria-controls="collapse{{$incarico->id}}">
                        {{ $incarico }}
                        <span class="badge badge-primary badge-pill">{{ $incarico->alunni()->count() }}</span>
                      </button>
                    </h5>
                  </div>
                    
                  <div id="collapse{{$incarico->id}}" class="collapse" aria-labelledby="heading{{$incarico->id}}" data-parent="#accordion">
                    <div class="card-body">
                      <ul>
                      @foreach($incarico->alunni as $lavoratore)
                        <li>@include('nomadelfia.templates.persona', ['persona'=>$lavoratore])</li>
                      @endforeach
                      </ul>
                      <div class="row">
                          <a class="btn btn-danger btn-block col-md-4 offset-md-2" type="button" href="{{ route('nomadelfia.incarichi.edit', $incarico->id)}}">Modifica</a>
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
