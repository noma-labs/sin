@extends("biblioteca.books.index")

@section("title", "Mappa della biblioteca")

@section("content")
  <style>
    @media print {
      @page {
        size: A4 landscape;
        margin: 8mm;
      }

      body > nav.navbar {
        display: none;
      }
    }
  </style>
  <div class="mt-2">
    <div class="d-flex flex-wrap align-items-baseline gap-2 mb-2">
      <h4 class="mb-0">Mappa della biblioteca</h4>
      <span class="small text-muted d-print-none"
        >Dove si trovano i libri e di cosa parla ogni colonna degli scaffali. Si
        può stampare con Ctrl+P.</span
      >
      <span class="small text-muted d-none d-print-inline"
        >stampata il {{ now()->format("d/m/Y") }}</span
      >
    </div>
    <livewire:mappa-biblioteca />
  </div>
@endsection
