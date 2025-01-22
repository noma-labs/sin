@extends("biblioteca.books.index")

@section("content")
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
                                nessun autore
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
                                nessun editore
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

                action="{{ route("books.destroy", $libro->id) }}"
            >
                {{ csrf_field() }}
                @method("DELETE")
                <div class="mb-3">
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
                <div class="mb-3">
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
