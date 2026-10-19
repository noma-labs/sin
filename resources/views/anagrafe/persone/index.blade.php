@extends("anagrafe.index")

@section("title", "Anagrafe")

@section("content")
  @include("partials.header", ["title" => "Anagrafe"])
  @include("anagrafe.persone.search_form")
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="btn-group" role="group" aria-label="Filtro persone">
      @foreach (["tutti" => "Tutti", "interni" => "In Nomadelfia", "esterni" => "Fuori Nomadelfia"] as $valore => $etichetta)
        <a
          class="btn btn-sm {{ $filtro === $valore ? "btn-primary" : "btn-outline-primary" }}"
          href="{{ route("anagrafe.index", ["filtro" => $valore]) }}"
        >
          {{ $etichetta }}
        </a>
      @endforeach
    </div>
    <div class="d-flex align-items-center gap-3">
      <span>
        Persone:
        <strong>{{ $persone->total() }}</strong>
      </span>
      @can("popolazione.persona.inserisci")
        <a class="btn btn-success" href="{{ route("anagrafe.person.create") }}">
          Inserisci persona
        </a>
      @endcan
    </div>
  </div>
  @include("anagrafe.persone.table")
@endsection
