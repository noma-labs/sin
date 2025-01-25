@extends("biblioteca.books.index")
@section("title", "Editori")

@section("content")
   @include("partials.header", ["title" => "Gestione Editori", "subtitle" =>  App\Biblioteca\Models\Editore::count()])

    <div class="row">
        <div class="col-md-8 offset-md-2 my-3">
            <span>Ricerca editore:</span>
            <form action="{{ route("editori.ricerca") }}" method="get">
                @csrf
                <livewire:search-editore name_input="idEditore" />
                <button class="btn btn-success my-2 float-right" type="submit">
                    Cerca
                </button>
            </form>
        </div>
    </div>
@endsection
