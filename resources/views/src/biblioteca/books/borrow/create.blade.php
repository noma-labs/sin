@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Prestito libro"])

    <form
        method="POST"
        class="form"
        action="{{ route("books.borrow.store", $libro->id) }}"
    >
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="xDatainizio" class="control-label">
                                Data Inizio Prestito
                            </label>
                            <input
                                class="form-control"
                                type="date"
                                value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                name="xDatainizio"
                                id="datep"
                                placeholder="Data inizio"
                            />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="xIdUtente" class="control-label">
                                Cliente
                            </label>
                            <livewire:search-popolazione
                                name_input="persona_id"
                            />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="note" class="control-label">Note</label>
                            <input
                                type="text"
                                class="form-control"
                                name="xNote"
                                placeholder="Inserisci le note"
                            />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <button
                            class="btn btn-success my-2"
                            name="biblioteca"
                            type="submit"
                        >
                            Conferma il prestito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="col-md-6">
        <div class="card border-info mb-3">
            <div class="card-header">Libro in prestito</div>
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
                        <strong>{{ $libro->autore }}</strong>
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
                        <strong>{{ $libro->editore }}</strong>
                    @endforelse
                </p>
                <p>
                    Classificazione:
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
@endsection
