@extends("biblioteca.books.index")
@section("title", "Classificazione")

@section("content")
    @include("partials.header", ["title" => "Gestione Classificazioni", "subtitle" => App\Biblioteca\Models\Classificazione::count()])

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <span>Ricerca classificazione:</span>
            <search-classificazione></search-classificazione>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <a
                href="{{ route("audience.create") }}"
                class="btn btn-success my-3"
            >
                Aggiungi Classificazione
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <table class="table table-hover table-bordered">
                <thead>
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
                                            href="{{ route("audience.edit", $classificazione->id) }}"
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
                                        <form
                                            action="{{ route("audience.destroy", $classificazione->id) }}"
                                            method="POST"
                                            style="display: inline"
                                        >
                                            @csrf
                                            @method("DELETE")
                                            <button
                                                type="submit"
                                                class="btn btn-danger"
                                            >
                                                Elimina
                                            </button>
                                        </form>
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
