@extends("officina.index")

@section("content")
    @include("partials.header", ["title" => "Patenti"])

    @include("patente.elenchi.percategoria")
@endsection
