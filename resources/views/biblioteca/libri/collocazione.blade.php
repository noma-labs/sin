@extends("biblioteca.libri.index")

@section("archivio")
    @include("partials.header", ["title" => "Modifica collocazione"])
    <form
        method="POST"
        action="{{ route("libro.collocazione.update", ["idLibro" => $libro->id]) }}"
    >
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="card text-white bg-info">
                    <div class="card-header">
                        <h2>Libro attuale</h2>
                    </div>
                    <div class="card-body">
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

            <div class="col-md-6">
                <h2>Scegli nuova collocazione</h2>
                <search-collocazione
                    url-lettere="{{ route("api.biblioteca.collocazione") }}"
                    numeri-required="true"
                    numeri-assegnati="true"
                ></search-collocazione>

                <div class="form-group">
                    <button class="btn btn-success" type="submit">
                        Modifica collocazione
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
