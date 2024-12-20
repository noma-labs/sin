@extends("biblioteca.libri.index")
@section("title", "Editori")

@section("content")
    <div class="my-page-title">
        <div class="d-flex justify-content-end">
            <div class="mr-auto p-2">
                <span class="h1 text-center">Gestione Editori</span>
            </div>
            <div class="p-2 text-right">
                <h5 class="m-1">
                    {{ App\Biblioteca\Models\Editore::count() }} Editori
                </h5>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2 my-3">
            <span>Ricerca editore:</span>
            <form action="{{ route("editori.ricerca") }}" method="get">
                {{ csrf_field() }}
                <livewire:search-editore name_input="idEditore" />
                <button class="btn btn-success my-2 float-right" type="submit">
                    Cerca
                </button>
            </form>
        </div>
    </div>
@endsection
