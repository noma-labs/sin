@extends("nomadelfia.index")

@section("title", "Persone")

@section("content")
  @include("partials.header", ["title" => "Persone", "subtitle" => $persone->total() . " persone"])
  @include("anagrafe.persone.search_form")
  <div class="d-flex justify-content-end gap-2 mb-3">
    <a class="btn btn-outline-primary" href="{{ route("anagrafe.families") }}">
      Famiglie
    </a>
    @can("popolazione.persona.inserisci")
      <a class="btn btn-success" href="{{ route("anagrafe.person.create") }}">
        Inserisci persona
      </a>
    @endcan
  </div>
  @include("anagrafe.persone.table")
@endsection
