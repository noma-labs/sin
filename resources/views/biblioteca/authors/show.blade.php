@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Autore"])

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label">Autore</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ $autore->autore }}"
                        disabled
                    />
                </div>
            </div>
            <a
                href="{{ route("autori.edit", $autore->id) }}"
                class="btn btn-info my-2"
            >
                Modifica
            </a>
            <a
                href="javascript:history.go(-1)"
                class="btn btn-primary my-2 float-right"
            >
                Torna indietro
            </a>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Libri Scritti ({{ $books->count() }})
                </div>
                <div class="card-body">
                    <ul>
                        @foreach ($books->get() as $libro)
                            <li>
                                <a
                                    href="{{ route("books.show", ["id" => $libro->id]) }}"
                                >
                                    {{ $libro->collocazione }} -
                                    {{ $libro->titolo }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- end section dettagli prenotazione -->
@endsection
