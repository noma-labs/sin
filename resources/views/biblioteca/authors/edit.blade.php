@extends("biblioteca.books.index")
@section("title", "Autori")

@section("content")
    @include("partials.header", ["title" => "Modifica Autore"])

    <div class="row">
        <div class="col-md-6 offset-md-2">
            <form
                action="{{ route("authors.update", $autore->id) }}"
                method="POST"
            >
                @csrf
                @method("PUT")
                <div class="">
                    <label for="autore">Autore</label>
                    <input
                        type="text"
                        name="autore"
                        id="autore"
                        class="form-control"
                        value="{{ $autore->autore }}"
                    />
                </div>
                <div class="my-2">
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
@endsection
