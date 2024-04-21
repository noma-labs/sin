@extends("biblioteca.libri.index")
@section("title", "Classificazione")

@section("content")
    <div class="my-page-title">
        <div class="d-flex justify-content-end">
            <div class="mr-auto p-2">
                <span class="h1 text-center">Gestione Classificazioni</span>
            </div>
            <div class="p-2 text-right">
                <h5 class="m-1">
                    <strong>
                        {{ App\Biblioteca\Models\Classificazione::count() }}
                    </strong>
                    Classificazioni
                </h5>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <span>Ricerca classificazione:</span>
            <search-classificazione></search-classificazione>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <a
                href="{{ route("classificazioni.create") }}"
                class="btn btn-success my-3"
            >
                Aggiungi Classificazione
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <table class="table table-hover table-bordered">
                <thead class="thead-inverse">
                    <tr>
                        <th>Classificazione</th>
                        <th>Operazioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classificazioni as $classificazione)
                        <tr>
                            <td>{{ $classificazione->descrizione }}</td>
                            <td>
                                <div
                                    class="btn-toolbar"
                                    role="toolbar"
                                    aria-label="..."
                                >
                                    <div
                                        class="btn-group"
                                        role="group"
                                        aria-label="..."
                                    >
                                        <a
                                            href="{{ route("classificazioni.edit", $classificazione->id) }}"
                                            class="btn btn-info pull-left"
                                        >
                                            Modifica
                                        </a>
                                    </div>
                                    <div
                                        class="btn-group"
                                        role="group"
                                        aria-label="..."
                                    >
                                        {!! Form::open(["method" => "DELETE", "route" => ["classificazioni.destroy", $classificazione->id]]) !!}
                                        {!! Form::submit("Elimina", ["class" => "btn btn-danger"]) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>Nessun autore presente</td>
                            <td></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $classificazioni->links() }}
        </div>
    </div>
@endsection
