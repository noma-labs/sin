@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Aziende'])
  @foreach($aziende as $azienda)
    <div id="accordion">
      <div class="card">
        <div class="card-header" id="heading{{$azienda->id}}">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$azienda->id}}" aria-expanded="true" aria-controls="collapse{{$azienda->id}}">
              {{ $azienda->nome_azienda }} (lavoratori: {{ $azienda->lavoratoriAttuali->count() }} )
            </button>
          </h5>
        </div>
          
        <div id="collapse{{$azienda->id}}" class="collapse" aria-labelledby="heading{{$azienda->id}}" data-parent="#accordion">
          <div class="card-body">
            <ul>
            @foreach($azienda->lavoratoriAttuali as $lavoratore)
              <li>{{ $lavoratore->nominativo }}</li>
            @endforeach
            </ul>
            <div class="row">
              <div class="col-md-2 offset-md-10">
                <a class="btn btn-danger btn-block" type="button" href="{{ route('nomadelfia.aziende.edit', $azienda->id)}}">Modifica</a>
              </div>
            </div>            
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endsection
