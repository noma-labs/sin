@extends("biblioteca.libri.index")
@section("title", "Autori")

@section("content")
    @include("partials.header", ["title" => "Modifica Autore"])

    <div class="row">
        <div class="col-md-6 offset-md-2">
            <form
                action="{{ route("autori.update", $autore->id) }}"
                method="POST"
                class="form-horizontal"
            >
                @csrf
                @method("PUT")
                <div class="form-group">
                    <label for="autore">Autore</label>
                    <input
                        type="text"
                        name="autore"
                        id="autore"
                        class="form-control"
                        value="{{ $autore->autore }}"
                    />
                </div>
                <div class="form-group my-2">
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
@endsection
