@extends("officina.index")

@section("title", "Prenotazioni")

@section("archivio")
    @include("partials.header", ["title" => "Ricerca Prenotazioni"])
    @include("officina.prenotazioni.search_form")
@endsection
