@extends("biblioteca.books.index")
@section("title", "Autori")

@section("content")
    @include("partials.header", ["title" => "Autori", "subtitle" => App\Biblioteca\Models\Autore::count()])

    <div class="row">
        <div class="col-md-8 offset-md-2 my-3">
            <form action="{{ route("authors.index") }}" method="get">
                @csrf
                <label for="term">Cerca Autore</label>
                <input type="text" name="term" class="form-control mb-3" />
                <button class="btn btn-success" type="submit">Cerca</button>
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
                    @foreach ($autori as $autore)
                        <tr>
                            <td>{{ $autore->id }}</td>
                            <td>{{ $autore->autore }}</td>
                            <td class="text-center">
                                <a
                                    href="{{ route("authors.show", $autore->id) }}"
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
