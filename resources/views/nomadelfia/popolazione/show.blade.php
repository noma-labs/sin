@extends("nomadelfia.index")

@section("archivio")
    @include("partials.header", ["title" => "Gestione Popolazione "])
    <livewire:popolazione-table></livewire:popolazione-table>
@endsection
