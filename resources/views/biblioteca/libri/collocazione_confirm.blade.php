@extends("biblioteca.libri.index")

@section("content")
    @include("partials.header", ["title" => "Inverti collocazione"])

    <div class="row">
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header">
                    <h2>Libro attuale</h2>
                </div>
                <div class="card-body">
                    <p>
                        Collocazione:
                        <strong>{{ $libro->collocazione }}</strong>
                        . Verrà sostituita con
                        <span class="bg-danger">
                            <strong>{{ $libroTarget->collocazione }}</strong>
                        </span>
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
                            <strong>Nessun autore</strong>
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
                            <strong>nessun editore</strong>
                        @endforelse
                    </p>
                    <p>
                        classificazione:
                        @if ($libro->classificazione)
                            <strong>
                                {{ $libro->classificazione->descrizione }}
                            </strong>
                        @endif
                    </p>
                    <p>
                        Note:
                        <strong>{{ $libro->note }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header">
                    <h2>Libro destinatario</h2>
                </div>
                <div class="card-body">
                    <p>
                        Collocazione:
                        <strong>{{ $libroTarget->collocazione }}</strong>
                        . Verrà sostituita con
                        <span class="bg-danger">
                            <strong>{{ $libro->collocazione }}</strong>
                        </span>
                    </p>
                    <p>
                        Titolo:
                        <strong>{{ $libroTarget->titolo }}</strong>
                    </p>
                    <p>
                        Autore:
                        <strong>{{ $libroTarget->autore }}</strong>
                    </p>
                    <p>
                        Editore:
                        <strong>{{ $libroTarget->editore }}</strong>
                    </p>
                    <p>
                        classificazione:
                        @if ($libroTarget->classificazione)
                            <strong>
                                {{ $libroTarget->classificazione->descrizione }}
                            </strong>
                        @endif
                    </p>
                    <p>
                        Note:
                        <strong>{{ $libroTarget->note }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form
                method="POST"
                class="form-inline"
                action="{{ route("books.call-number.swap.update", ["id" => $libro->id, "idTarget" => $libroTarget->id]) }}"
            >
                @method("PUT")
                {{ csrf_field() }}
                <div class="form-group">
                    <button class="btn btn-danger mx-1" type="submit">
                        Cambia collocazione
                    </button>
                    <a
                        class="btn btn-info"
                        href="#"
                        onclick="window.history.back(); return false;"
                    >
                        No, torna indietro
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
