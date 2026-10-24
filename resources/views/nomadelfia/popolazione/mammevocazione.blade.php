@extends("nomadelfia.index")

@section("content")
  @include("partials.header", ["title" => "Gestione Mamme Di Vocazione"])
  <div class="row justify-content-md-center">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-header">
          <h5>
            Mammae di vocazione
            <span class="badge bg-primary"> {{ count($mvocazione) }} </span>
          </h5>
        </div>
        <div class="card-body">
          <div class="row">
            <p class="col-md-4 fw-bold">Nominativo</p>
            <p class="col-md-4 fw-bold">Data creazione famiglia</p>
            <p class="col-md-4 fw-bold">Durata</p>
          </div>

          @forelse ($mvocazione as $mamma)
            <div class="row">
              <p class="col-md-4">
                @include("nomadelfia.templates.persona", ["persona" => $mamma])
              </p>
              <p class="col-md-4">{{ $mamma->data_creazione }}</p>
              <p class="col-md-4">
                @if ($mamma->data_creazione)
                  @diffHumans($mamma->data_creazione)
                @endif
              </p>
            </div>
          @empty
            <p class="text-danger">Nessun mamma di vocazione</p>
          @endforelse
        </div>
        <!--end card body-->
      </div>
      <!--end card -->
    </div>
  </div>
@endsection
