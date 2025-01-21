@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Modifica collocazione"])
    <form
        method="POST"
        action="{{ route("books.call-number.update", ["id" => $libro->id]) }}"
    >
        {{ csrf_field() }}
        @method("PUT")
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
                                <strong>Neesun editore</strong>
                            @endforelse
                        </p>
                        <p>
                            classificazione:
                            @if ($libro->classificazione)
                                <strong>
                                    {{ $libro->classificazione->descrizione }}
                                </strong>
                            @else
                                <strong>Nessuna classificazione</strong>
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
                <h2>Scegli nuova collocazione</h2>
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Collocazione -lettere</label>
                        <livewire:search-collocazione-lettere />
                    </div>
                    <div class="col-md-4">
                        <livewire:search-collocazione-numeri
                            :show-free="false"
                            :show-busy="true"
                            :show-next="true"
                            name="xCollocazione"
                        />
                    </div>
                </div>

                <div class="mb-3">
                    <button class="btn btn-success" type="submit">
                        Modifica collocazione
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
