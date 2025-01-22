@extends("biblioteca.books.index")
@section("title", "Autori")

@section("content")
    <div class="my-page-title">
        <div class="d-flex justify-content-end">
            <div class="mr-auto p-2">
                <span class="h1 text-center">Ricerca Autori</span>
            </div>
            <div class="p-2 text-right">
                <h5 class="m-1">
                    {{ App\Biblioteca\Models\Autore::count() }} Autori
                </h5>
            </div>
        </div>
    </div>

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
