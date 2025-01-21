@extends("nomadelfia.index")

@section("title", "Persone")

@section("content")
    @include("partials.header", ["title" => "Ricerca Persone"])
    @include("nomadelfia.persone.search_form")
@endsection
