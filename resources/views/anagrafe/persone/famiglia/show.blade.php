@extends("nomadelfia.index")

@section("content")
  @include("partials.header", ["title" => "Gestione Famiglie di " . $persona->nominativo])
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Famiglia Attuale</div>
        <div class="card-body">
          @if ($attuali->count() > 1)
            <div class="alert alert-warning" role="alert">
              {{ $persona->nominativo }} è nel nucleo di {{ $attuali->count() }} famiglie.
              Scegli quale mantenere come famiglia attuale: dalle altre esce dal
              nucleo e restano nel suo storico.
            </div>
          @endif

          @if ($attuali->isNotEmpty())
            <div class="row">
              <p class="col-md-4 fw-bold">Nome Famiglia</p>
              <p class="col-md-4 fw-bold">Posizione Famiglia</p>
              @if ($attuali->count() > 1)
                <p class="col-md-4 fw-bold">Operazioni</p>
              @endif
            </div>
            @foreach ($attuali as $attuale)
              <div class="row align-items-center">
                <p class="col-md-4">
                  <a href="{{ route("anagrafe.families.show", $attuale->id) }}">
                    {{ $attuale->nome_famiglia }}
                  </a>
                </p>
                <p class="col-md-4">
                  {{ $attuale->pivot->posizione_famiglia }}
                </p>
                @if ($attuali->count() > 1)
                  <div class="col-md-4 mb-3">
                    @can("popolazione.persona.modifica")
                      <form
                        method="POST"
                        action="{{ route("anagrafe.person.families.attuale", $persona->id) }}"
                      >
                        @csrf
                        @method("PUT")
                        <input
                          type="hidden"
                          name="famiglia_id"
                          value="{{ $attuale->id }}"
                        />
                        <button
                          type="submit"
                          class="btn btn-sm btn-warning"
                          onclick="return confirm(@js("Mantenere {$attuale->nome_famiglia} come famiglia attuale? Dalle altre famiglie {$persona->nominativo} passa nello storico."))"
                        >
                          Mantieni questa
                        </button>
                      </form>
                    @endcan
                  </div>
                @endif
              </div>
            @endforeach
          @else
            <div class="row">
              <p class="text-danger">Nessuna famiglia</p>
            </div>
          @endif
        </div>
      </div>

      <div class="card my-3">
        <div class="card-header">Storico famiglie</div>
        <div class="card-body">
          <div class="row">
            <p class="col-md-3 fw-bold">Nome famiglia</p>
            <p class="col-md-3 fw-bold">Posizione</p>
          </div>

          @forelse ($storico as $famigliaStorico)
            <div class="row">
              <p class="col-md-3">
                <a
                  href="{{ route("anagrafe.families.show", $famigliaStorico->id) }}"
                >
                  {{ $famigliaStorico->nome_famiglia }}
                </a>
              </p>
              <p class="col-md-3">
                {{ $famigliaStorico->pivot->posizione_famiglia }}
              </p>
            </div>
          @empty
            <p class="text-danger">Nessuna famiglia nello storico</p>
          @endforelse
        </div>
        <!--end card body-->
      </div>
    </div>
  </div>
@endsection
