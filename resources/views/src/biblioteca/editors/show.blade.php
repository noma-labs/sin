@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Editore"])

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <label>Editore</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ $editore->editore }}"
                        disabled
                    />
                </div>
            </div>
            <a
                href="{{ route("editori.edit", $editore->id) }}"
                class="btn btn-info my-2"
            >
                Modifica
            </a>
            <a
                href="javascript:history.go(-1)"
                class="btn btn-primary my-2 float-end"
            >
                Torna indietro
            </a>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Libri pubblicati ({{ $books->count() }})
                </div>
                <div class="card-body">
                    @if ($books->count())
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
                    @else
                        <p class="bg-danger">Nessun libro</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- end section dettagli prenotazione -->
@endsection
