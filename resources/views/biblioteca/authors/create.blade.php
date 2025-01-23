@extends("biblioteca.books.index")
@section("title", "Aggiungi Autore")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Autore"])
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-2">
                <form action="{{ route("autori.store") }}" method="POST">
                    @csrf
                    <div class="">
                        <label for="autore">Autore</label>
                        <input
                            type="text"
                            name="autore"
                            id="autore"
                            class="form-control"
                            placeholder="Es. Italo Calvino"
                        />
                    </div>
                    <div class=" my-3">
                        <button type="submit" class="btn btn-primary">
                            Aggiungi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
