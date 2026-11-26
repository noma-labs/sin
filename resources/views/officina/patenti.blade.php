@extends("officina.index")

@section("title", "Patenti")

@section("content")
  @include("partials.header", ["title" => "Patenti"])
  @include("patente.elenchi.percategoria")
@endsection
