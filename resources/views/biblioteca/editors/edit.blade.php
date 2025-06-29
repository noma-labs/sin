@extends("biblioteca.books.index")
@section("title", "Editori")

@section("content")
    @include("partials.header", ["title" => "Modifica Editore"])

    <div class="row">
        <div class="col-md-6 offset-md-2">
            <form
                action="{{ route("editors.update", $editore->id) }}"
                method="POST"
            >
                @csrf
                @method("PUT")
                <div class="">
                    <label for="editore">Editore</label>
                    <input
                        type="text"
                        name="editore"
                        id="editore"
                        class="form-control"
                        value="{{ $editore->editore }}"
                    />
                </div>
                <div class="">
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
@endsection
