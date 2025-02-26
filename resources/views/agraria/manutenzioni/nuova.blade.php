@extends("layout.main")

@section("title", "Manutenzione")

@section("content")
    @include("partials.header", ["title" => "Nuova Manutenzione"])
    <nuova-manutenzione-form
        programmate-url="{{ route("api.manutenzioni.programmate") }}"
        mezzi-url="{{ route("api.mezzi") }}"
    ></nuova-manutenzione-form>
@endsection
