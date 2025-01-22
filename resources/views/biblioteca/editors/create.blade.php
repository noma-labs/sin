@extends("biblioteca.books.index")
@section("title", "Aggiungi Editore")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Editore"])

    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-2">
                <form action="{{ route("editori.store") }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="editore">Editore</label>
                        <input
                            type="text"
                            name="editore"
                            id="editore"
                            class="form-control"
                            placeholder="Es. Mondadori"
                        />
                    </div>
                    <div class="form-group my-3">
                        <button type="submit" class="btn btn-primary">
                            Aggiungi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
