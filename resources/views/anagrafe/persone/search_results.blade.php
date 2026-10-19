@extends("anagrafe.index")

@section("title", "Ricerca Persone")

@section("content")
  @include("partials.header", ["title" => "Ricerca Persone"])
  @include("anagrafe.persone.search_form")
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    Ricerca effettuata:
    <strong>{{ $msgSearch }}</strong>
    <button
      type="button"
      class="btn-close"
      data-bs-dismiss="alert"
      aria-label="Close"
    ></button>
  </div>
  <div id="results" class="alert alert-success">
    Numero di persone trovate:
    <strong>{{ $persone->total() }}</strong>
  </div>
  @include("anagrafe.persone.table")
@endsection
