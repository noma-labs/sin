@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Prestito"])

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="xDataPrenotazione">
                            Data Inizio Prestito
                        </label>
                        <input
                            class="form-control"
                            value="{{ $prestito->data_inizio_prestito }}"
                            name="xDataPrenotazione"
                            id="datep"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="xIdUtente">
                            Cliente
                        </label>
                        <p>
                            @include("nomadelfia.templates.persona", ["persona" => $prestito->cliente])
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Note</label>
                        <input
                            class="form-control"
                            value="{{ $prestito->note }}"
                            name="xnote"
                            id="datep"
                            disabled
                        />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label" for="xIdBibliotecario">
                            Bibliotecario
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="autore"
                            value="{{ $prestito->Bibliotecario->nominativo }}"
                            disabled
                        />
                    </div>
                </div>
            </div>

            <form
                class="form"
                id="concludi-prestito"
                method="POST"
                action="{{ route("books.loans.return", $prestito->id) }}"
            >
                {{ csrf_field() }}
                @method("PUT")
            </form>

            <div class="row">
                <div class="col-md-12 my-3">
                    <a
                        class="btn btn-success"
                        href="{{ route("books.loans.edit", ["id" => $prestito->id]) }}"
                        role="button"
                    >
                        Modifica Prestito
                    </a>
                    @if ($prestito->in_prestito)
                        <button
                            class="btn btn-primary"
                            form="concludi-prestito"
                            name="_concludi"
                            value="true"
                            type="submit"
                        >
                            Concludi Prestito
                        </button>
                    @endif

                    <a
                        class="btn btn-info float-right"
                        href="{{ route("books.loans") }}"
                        role="button"
                    >
                        Torna ai prestiti
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-info mb-3">
                <div class="card-header">Libro in prestito</div>
                <div class="card-body">
                    <p>
                        Collocazione:
                        <strong>{{ $prestito->libro->collocazione }}</strong>
                    </p>
                    <p>
                        Titolo:
                        <strong>{{ $prestito->libro->titolo }}</strong>
                    </p>
                    <p>
                        Autore:
                        <strong>{{ $prestito->libro->autore }}</strong>
                        @foreach ($prestito->libro->autori as $autore)
                            @if ($loop->last)
                                <strong>{{ $autore->autore }}</strong>
                                ,
                            @else
                                <strong>{{ $autore->autore }}</strong>
                            @endif
                        @endforeach
                    </p>
                    <p>
                        Editore:
                        <strong>{{ $prestito->libro->editore }}</strong>
                        @foreach ($prestito->libro->editori as $editore)
                            @if ($loop->last)
                                <strong>{{ $editore->editore }}</strong>
                                ,
                            @else
                                <strong>{{ $editore->editore }}</strong>
                            @endif
                        @endforeach
                    </p>
                    <p>
                        Note:
                        <strong>{{ $prestito->libro->note }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
