@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Studente"])

    {{$student}}
@endsection
