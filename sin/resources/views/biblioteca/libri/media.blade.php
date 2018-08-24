@extends('biblioteca.libri.index')

@section('archivio')
@include('partials.header', ['title' => 'Libro formato digitale'])

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">File digitali caricati</h3>
    </div>

    <div class="panel-body">
      @forelse ($libro->getMedia()  as $file)
            <div class="list-group-item">
                <div class="media">
                    <div class="media-left">
                           @if(starts_with($file->mime_type, 'image'))
                            <a href="{{ $file->getUrl() }}" target="_blank">
                                <img class="media-object" style="width:150px" src="{{ $file->getUrl() }}" alt="{{ $file->name }}">
                            </a>
                            @elseif (str_contains($file->mime_type,'pdf'))
                            <a href="{{ $file->getUrl() }}" target="_blank">
                                <span class="glyphicon glyphicon-file" style="font-size:48px"></span>
                              <!-- <i class="fa fa-file-pdf-o" style="font-size:48px"></i> -->
                            </a>
                            @else
                            <a href="{{ $file->getUrl() }}" target="_blank">
                                <span class="glyphicon glyphicon-play-circle" style="font-size:48px"></span>
                              <!-- <i class="fa fa-file-pdf-o" style="font-size:48px"></i> -->
                            </a>
                            <!-- <i class="fa fa-file" aria-hidden="true" style="font-size:48px"></i> -->
                            @endif
                    </div>
                    <div class="media-body">
                        <div class="btn-group pull-right">
                            <a href="{{ route("libri.media.destroy",[$libro->id, $file->id]) }}"
                               data-method="delete"
                               data-token="{{ csrf_token() }}"
                               class="close">
                               <span class="glyphicon glyphicon-trash"></span>
                              <!-- <i class="fa fa-times" aria-hidden="true"  style="font-size:30px"></i> -->
                            </a>
                        </div>
                        <h4 class="media-heading">{{ $file->name }}</h4>
                        <p>
                            <code>
                                {{ $file->getPath() }}<br/>
                            </code>
                            <small>
                                {{ $file->human_readable_size }} |
                                {{ $file->mime_type }}
                            </small>
                        </p>

                        <!-- @foreach($file->getMediaConversionNames() as $conversion)
                            <div class="media">
                                <div class="media-left">
                                    <a href="{{ $file->getUrl($conversion) }}" target="_blank">
                                        <img class="media-object media-object-small"
                                             src="{{ $file->getUrl($conversion) }}" alt="{{ $conversion }}">
                                    </a>
                                </div>
                                <div class="media-body media-middle">
                                    <h4 class="media-heading">{{ $conversion }}</h4>
                                </div>
                            </div>
                        @endforeach -->

                    </div>
                </div>
            </div>
        @empty
            <p class="text-danger">Nessun file digitale esistente</p>
        @endforelse

    </div>

 @if (!Auth::guest())
    @if(count($libro->getMedia()) > 0)
        <div class="panel-footer">
            <a href="{{ route('libri.media.destroy_all', $libro->id) }}"
               class="btn btn-sm btn-danger"
               data-method="delete"
               data-confirm="Sei sicuro di eliminare tutti i media?"
               data-token="{{ csrf_token() }}">
                Cancella tutti
            </a>
        </div>
    @endif
  @endif

</div>
 @if (!Auth::guest())
  <div class="panel panel-default">
      <div class="panel-heading">
          <h3 class="panel-title">Carica i file digitali</h3>
      </div>
      <div class="panel-body">
          <form action="{{ route('libri.media.store', $libro->id) }}"
                class="dropzone"
                id="my-awesome-dropzone"  >
              {{ csrf_field() }}
          </form>
      </div>
  </div>
@endif

<div class="btn-toolbar pull-right">
  <!-- <a class="btn btn-info"   href="{{route('libri.ricerca')}}"  type="submit">Torna Alla ricerca</a> -->
  <a class="btn btn-info" href="{{ route('libro.dettaglio',$libro->id)}} " >Torna indietro</a>
</div>
@endsection
