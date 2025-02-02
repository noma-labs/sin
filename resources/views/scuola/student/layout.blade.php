@extends("layouts.app-sidebar")

@section("header")
    @include("partials.header", ["title" => "Alunno: ". $student->nome . " " . $student->cognome])
@endsection
