@extends("biblioteca.libri.index")

@section("archivio")
    @include("partials.header", ["title" => "Eliminazione libro"])
    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-danger">
                <div class="card-header">
                    <h1>Libro da eliminare</h1>
                </div>
                <div class="card-body">
                    <div class="card-text">
                        <p>
                            Collocazione:
                            <strong>{{ $libro->collocazione }}</strong>
                        </p>
                        <p>
                            Titolo:
                            <strong>{{ $libro->titolo }}</strong>
                        </p>
                        <p>
                            Autore:
                            @forelse ($libro->autori as $autore)
                                @if ($loop->last)
                                    <strong>{{ $autore->autore }}</strong>
                                    ,
                                @else
                                    <strong>{{ $autore->autore }}</strong>
                                @endif
                            @empty
                                <strong>{{ $libro->AUTORE }}</strong>
                            @endforelse
                        </p>
                        <p>
                            Editore:
                            @forelse ($libro->editori as $editore)
                                @if ($loop->last)
                                    <strong>{{ $editore->editore }}</strong>
                                    ,
                                @else
                                    <strong>{{ $editore->editore }}</strong>
                                @endif
                            @empty
                                <strong>{{ $libro->EDITORE }}</strong>
                            @endforelse
                        </p>
                        <p>
                            classificazione:
                            <strong>
                                {{ $libro->classificazione->descrizione }}
                            </strong>
                        </p>
                        <p>
                            Note:
                            <strong>{{ $libro->note }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2>Motivazione cancellazione</h2>
            <form
                method="POST"
                class="form-horizontal"
                action="/biblioteca/libri/{{ $libro->id }}/elimina"
            >
                {{ csrf_field() }}
                <div class="form-group">
                    <label classfor="motivo">
                        Inserisci la motivazione della cancellazione
                    </label>
                    <input
                        class="form-control"
                        type="text"
                        name="xCancellazioneNote"
                        placeholder="Aggiungi una motivazione. Es. Non adatto o doppione"
                    />
                </div>
                <input type="hidden" name="post" value="yes" />
                <!-- value=yes sent to the server, if value is yes delete the libro -->
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        Elimina
                    </button>
                </div>
            </form>
        </div>
    </div>
    <a
        class="btn btn-info my-2"
        href="#"
        onclick="window.history.back(); return false;"
    >
        No, torna indietro
    </a>
@endsection
