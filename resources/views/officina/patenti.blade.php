@extends("officina.index")

@section("archivio")
    @include("partials.header", ["title" => "Patenti"])

    @include("patente.elenchi.percategoria")
@endsection
