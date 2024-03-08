@extends("nomadelfia.index")

@section("archivio")
    @include("partials.header", ["title" => "Gestione Popolazione "])
    @livewire("popolazione-table")
@endsection
