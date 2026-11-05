@extends("biblioteca.books.index")

@section("title", "Mappa della biblioteca")

@section("content")
  <div class="mt-2">
    <div class="d-flex flex-wrap align-items-baseline gap-2 mb-2">
      <h4 class="mb-0">Mappa della biblioteca</h4>
      <span class="small text-muted"
        >Dove si trovano i libri e di cosa parla ogni colonna degli
        scaffali.</span
      >
    </div>
    <livewire:mappa-biblioteca />
  </div>
@endsection
