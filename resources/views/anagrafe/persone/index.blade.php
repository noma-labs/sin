@extends("nomadelfia.index")

@section("title", "Persone")

@section("content")
  @include("partials.header", ["title" => "Persone", "subtitle" => $persone->total() . " persone"])
  @include("anagrafe.persone.search_form")
  @include("anagrafe.persone.table")
@endsection
