@extends("biblioteca.books.index")
@section("title", "Classificazione")

@section("content")
    @include("partials.header", ["title" => "Modifica Classificazione"])

    <div class="row">
        <div class="col-md-6 offset-md-4">
            <form
                action="{{ route("classificazioni.update", $classificazione->id) }}"
                method="POST"
            >
                @csrf
                @method("PUT")
                <div class="">
                    <label for="descrizione">Classificazione</label>
                    <input
                        type="text"
                        name="descrizione"
                        id="descrizione"
                        class="form-control"
                        value="{{ $classificazione->descrizione }}"
                    />
                </div>
                <div class="">
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
@endsection
