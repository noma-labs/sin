@extends("biblioteca.books.index")
@section("title", "Editori")

@section("content")
    @include("partials.header", ["title" => "Modifica Editore"])

    <div class="row">
        <div class="col-md-6 offset-md-2">
            <form
                action="{{ route("editori.update", $editore->id) }}"
                method="POST"
            >
                @csrf
                @method("PUT")
                <div class="mb-3">
                    <label for="editore">Editore</label>
                    <input
                        type="text"
                        name="editore"
                        id="editore"
                        class="form-control"
                        value="{{ $editore->editore }}"
                    />
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
@endsection
