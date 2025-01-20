@extends("biblioteca.books.index")
@section("title", "Aggiungi Classificazione")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Classificazione"])

    <div class="row">
        <div class="col-md-4 offset-md-4">
            <form
                action="{{ route("classificazioni.index") }}"
                method="POST"
                class="form-horizontal"
            >
                @csrf
                <div class="form-group">
                    <label for="descrizione">Classificazione</label>
                    <input
                        type="text"
                        name="descrizione"
                        id="descrizione"
                        class="form-control"
                        placeholder="Nome classificazione"
                    />
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        Aggiungi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
