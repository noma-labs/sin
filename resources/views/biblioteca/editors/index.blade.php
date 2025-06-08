@extends("biblioteca.books.index")
@section("title", "Editori")

@section("content")
    @include("partials.header", ["title" => "Gestione Editori", "subtitle" => App\Biblioteca\Models\Editore::count()])

    <div class="row">
        <div class="col-md-8 offset-md-2 my-3">
            <form action="{{ route("editors.index") }}" method="get">
                @csrf
                <label for="term">Cerca Editore</label>
                <input type="text" name="term" class="form-control mb-3" />
                <button class="btn btn-success my-2 float-end" type="submit">
                    Cerca
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Autore</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($editori as $editore)
                        <tr>
                            <td>{{ $editore->id }}</td>
                            <td>{{ $editore->editore }}</td>
                            <td class="text-center">
                                <a
                                    href="{{ route("editors.show", $editore->id) }}"
                                    class="btn btn-primary"
                                >
                                    Visualizza
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
