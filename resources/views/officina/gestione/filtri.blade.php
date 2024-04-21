@extends("officina.index")

@section("title", "Filtri")

@section("content")
    @include("partials.header", ["title" => "Gestione Filtri"])
    <gestione-filtri></gestione-filtri>
@endsection
