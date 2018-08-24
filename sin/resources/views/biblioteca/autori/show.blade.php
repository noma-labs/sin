@extends('biblioteca.libri.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Autore'])

<div class="row">

  <div class="col-md-6">
    <div class="row">
      <div class="col-md-12">
        <label >Autore</label>
        <input type="text" class="form-control" value="{{$autore->autore}}" disabled>
        </div>
    </div>
    <a href="{{ route('autori.edit', $autore->id) }}" class="btn btn-info my-2" >Modifica</a>
    <a href="javascript:history.go(-1)" class="btn btn-primary my-2 float-right">Torna indietro</a>

  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        Libri Scritti ({{$autore->libri->count()}})
      </div>
      <div class="card-body">
          @if($autore->libri->count())
          <ul>
            @foreach ($autore->libri()->orderBy("titolo")->get() as $libro)
                <li>
                  <a href="{{route('libro.dettaglio', ['idLibro' => $libro->id] )}}">{{ $libro->collocazione }} - {{ $libro->titolo }} </a>
                </li>
            @endforeach
        </ul>
        @else
        <p class="bg-danger">Nessun libro </p>
        @endif
      </div>

    </div>
  </div>
  </div>
<!-- end section dettagli prenotazione -->
@endsection
