@extends("biblioteca.books.index")

@section("title", "Mappa della biblioteca")

@section("content")
  <div class="mt-3">
    <h2 class="mb-1">Mappa della biblioteca</h2>
    <p class="text-muted">Dove si trovano i libri e di cosa parla ogni colonna degli scaffali.</p>
    <livewire:mappa-biblioteca />
  </div>
@endsection
