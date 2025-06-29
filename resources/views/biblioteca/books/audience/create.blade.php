@extends("biblioteca.books.index")
@section("title", "Aggiungi Classificazione")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Classificazione"])

    <div class="row">
        <div class="col-md-4 offset-md-4">
            <form action="{{ route("audience.store") }}" method="POST">
                @csrf
                <div class="">
                    <label for="descrizione">Classificazione</label>
                    <input
                        type="text"
                        name="descrizione"
                        id="descrizione"
                        class="form-control"
                        placeholder="Nome classificazione"
                    />
                </div>
                <div class="">
                    <button type="submit" class="btn btn-primary">
                        Aggiungi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
