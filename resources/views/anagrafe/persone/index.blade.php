@extends("anagrafe.index")

@section("title", "Anagrafe")

@section("content")
  @include("partials.header", ["title" => "Anagrafe", "subtitle" => $persone->total() . " persone"])
  @include("anagrafe.persone.search_form")
  @can("popolazione.persona.inserisci")
    <div class="d-flex justify-content-end mb-3">
      <a class="btn btn-success" href="{{ route("anagrafe.person.create") }}">
        Inserisci persona
      </a>
    </div>
  @endcan
  @include("anagrafe.persone.table")
@endsection
