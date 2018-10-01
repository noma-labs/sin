@extends('biblioteca.libri.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione libro'])

<div class="row">
  <div class="col-md-8">
    @if($libro->trashed())
      <div class="p-3 mb-2 bg-danger text-white">
        <h2>Libro Eliminato.</h2>
        Motivazione: {{$libro->deleted_note}}
        <form id="restoreBook"  action="{{route('libri.ripristina', ['idLibro' => $libro->id])}}" method="post">
          {{ csrf_field() }}
          <button form="restoreBook" class="btn btn-info my-2"> Ripristina Libro</button>
        </form>
      </div>
    @endif
    <div class="row">
      <div class="col-md-6">
        <label>Collocazione</label>
        <input type="text" class="form-control"value="{{$libro->collocazione}}" disabled>
     </div>
     <div class=" col-md-6">
        <label for="titolo">Titolo</label>
        <input type="text" class="form-control" id="titolo" value="{{$libro->titolo}}" disabled>
      </div>
    </div>
   <div class="row">
     <div class="col-md-6">
        <label for="autore">Autori </label>
          <autocomplete :selected="{{$libro->autori()->pluck('autore','id')}}"
                        :multiple="true"
                       :disabled="true">
           </autocomplete>
      </div>
      <div class="col-md-6">
         <label for="autore">Editori</label>
         <autocomplete :selected="{{$libro->editori()->pluck('editore','id')}}"
                       :multiple="true"
                      :disabled="true">
          </autocomplete>
       </div>
  </div>
     <!-- End second row -->
     <div class="row">
     <div class="col-md-12">
        <label for="autore">Classificazione</label>
        <input type="text" class="form-control" id="autore" value="{{$libro->classificazione->descrizione}}" disabled>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <label for="isbn">ISBN</label>
        <input class="form-control" type="text" value="{{$libro->isbn}}" disabled>
      </div>
      <div class="col-md-4">
        <label >Data pubblicazione</label>
        <input class="form-control" type="date" value="{{$libro->data_pubblicazione}}" disabled>
      </div>
      <div class="col-md-4">
        <label >Categoria</label>
        <input class="form-control" type="text" value="{{$libro->categoria}}" disabled>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label >Dimensione</label>
        <input class="form-control" type="text" value="{{$libro->dimensione}}" disabled>
      </div>
      <div class="col-md-6">
        <label >Critica</label>
        <input class="form-control" type="text" value="{{$libro->critica}}" disabled>
      </div>
    </div>

    <!-- End row  class-->
     <div class="row">
      <div class="col-md-12">
         <label for="autore">Note</label>
         <input type="text" class="form-control" id="autore" value="{{$libro->note}}" disabled>
       </div>
     </div>
    <!-- End note  class-->
    <form id="addLibroPrint" action="{{route('libri.etichette.aggiungi.libro',['idLibro' => $libro->id])}}" method="post">
      {{ csrf_field() }}
    </form>

    <form id="removeLibroPrint" action="{{route('libri.etichette.rimuovi.libro',['idLibro' => $libro->id])}}" method="post">
      {{ csrf_field() }}
    </form>

    <div class="row my-2">
     <div class="col-md-6">
           @if(!$libro->trashed())
            <a class="btn btn-success"  href="{{route('libro.modifica', ['idLibro' => $libro->id])}}" >Modifica</a>
            @if($libro->tobe_printed == 0)
            <button class="btn btn-success"  form="addLibroPrint" type="submit">Aggiungi stampa etichetta</button>
            @else
            <button class="btn btn-warning" form="removeLibroPrint" type="submit">Rimuovi stampa etichetta</button>
            @endif
       </div>
       <div class="col-md-6">
            <form action="{{route('libri.stampaetichetta', ['idLibro' => $libro->id])}}" id="printEtichetta">
            </form>
            <button class="btn btn-warning" form="printEtichetta" type="submit">Genera etichetta</button>
          @endif
          <a class="btn btn-info" href="#" onclick="window.history.back(); return false;">Torna indietro</a>
      </div>
    </div>
    <!-- End buttons  row -->

  </div>
  <!-- end column book info -->

  <div class="col-md-4">
    <!-- <div class="card-columns"> -->
      <div class="card card-info border-info " >
        <div class="card-header">Prestiti attivo</div>
        <div class="card-body">
            @forelse ($prestitiAttivi as $prestito)
                <p>Cliente: <strong>{{$prestito->cliente->nominativo}} </strong></p>
                <p>Data Inizio Prestito: <strong>{{$prestito->data_inizio_prestito}} </strong></p>
                <p>Data Fine Prestito: <strong>{{$prestito->data_fine_prestito}}</strong></p>
                <p>Bibliotecario: <strong>{{$prestito->bibliotecario->nominativo}}</strong></p>
            @empty
            <p class="bg-danger">Nessuna prenotazione attiva</p>
            @endforelse

          @if ($libro->inPrestito())
          <a class="btn btn-primary" href="{{route('libri.prestito', ['idPrestito' => $prestito->id])}}">Gestisci prestito</a>
          @else
           <a class="btn btn-primary" href="{{route('libri.prenota', ['idLibro' => $libro->id])}}">Dai in Prestito</a>
           @endif
          </div>
      </div>

    <div class="card border-warning my-2" >
      <div class="card-header">Versione Digitale</div>
      <div class="card-body">
        @if($libro->getMedia()->count() > 0)
          <div class="list-group-item">
          @foreach  ($libro->getMedia()  as $file)
            <div class="media">
                <div class="media-left">
                       @if(starts_with($file->mime_type, 'image'))
                        <a href="{{ $file->getUrl() }}" target="_blank">
                            <img class="media-object" style="width:75px" src="{{ $file->getUrl() }}" alt="{{ $file->name }}">
                        </a>
                        @elseif (str_contains($file->mime_type,'pdf'))
                        <a href="{{ $file->getUrl() }}" target="_blank">
                            <span class="glyphicon glyphicon-file" style="font-size:48px"></span>
                          <!-- <i class="fa fa-file-pdf-o" style="font-size:48px"></i> -->
                        </a>
                        @else
                        <i class="fa fa-file" aria-hidden="true" style="font-size:48px"></i>
                        @endif
                </div>
                <div class="media-body">
                    <h4 class="media-heading">{{ $file->name }}</h4>
                    <p>
                        <!-- <code>
                            {{ $file->getPath() }}<br/>
                        </code> -->
                        <small>
                            {{ $file->human_readable_size }} |
                            {{ $file->mime_type }}
                        </small>
                    </p>
                </div>
            </div>
          @endforeach
            </div>
        @else
        <p class="bg-danger">Nessun file digitale esistente</p>
        @endif
        <a class="btn btn-warning"   href="{{route('libri.media.store', $libro->id)}}" > Gestisci digitale</a>
       </div>
      </div>
    </div>
  <!-- </div> -->
</div>

<!-- end section dettagli prenotazione -->
@endsection
