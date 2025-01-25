@extends("biblioteca.books.index")
@section("title", "Autori")

@section("content")
    @include("partials.header", ["title" => "Ricerca Autori", "subtitle" => App\Biblioteca\Models\Autore::count()])

    <div class="row">
        <div class="col-md-8 offset-md-2 my-3">
            <span>Ricerca autore:</span>
            <form action="{{ route("autori.ricerca") }}" method="get">
                @csrf
                <livewire:search-autore name_input="idAutore" />
                <button class="btn btn-success my-2 float-right" type="submit">
                    Cerca
                </button>
            </form>
        </div>
    </div>

@endsection
