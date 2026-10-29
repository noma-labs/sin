@extends("nomadelfia.index")

@section("content")
  <div class="person-profile">
    <header class="profile-band profile-band-solo rounded-3 mb-4">
      <div class="d-flex flex-wrap gap-3 align-items-center">
        <div class="flex-grow-1" style="min-width: 0">
          <h1 class="profile-name mb-0">Famiglie</h1>
          <div class="profile-muted">
            {{ $colonne->sum(fn ($famiglie) => $famiglie->count()) }} {{ $tutteLeFamiglie ? "famiglie in anagrafe" : "famiglie in Nomadelfia" }}
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          @if ($tutteLeFamiglie)
            <a
              class="btn profile-btn"
              href="{{ route("anagrafe.families", request()->except("famiglie")) }}"
            >
              Solo in Nomadelfia
            </a>
          @else
            <a
              class="btn profile-btn"
              href="{{ route("anagrafe.families", [...request()->query(), "famiglie" => "tutte"]) }}"
            >
              Mostra tutte
            </a>
          @endif
          @can("popolazione.persona.inserisci")
            <a
              class="btn btn-success"
              href="{{ route("anagrafe.families.create") }}"
            >
              Nuova famiglia
            </a>
          @endcan
        </div>
      </div>
    </header>

    <section class="mb-4" aria-labelledby="titolo-famiglie">
      <h2 id="titolo-famiglie" class="visually-hidden">Famiglie per tipo</h2>
      <div class="row row-cols-1 row-cols-md-3 row-cols-xxl-5 g-3">
        @foreach ($colonne as $tipo => $famiglie)
          <div class="col">
            <div class="card h-100">
              <div
                class="card-header d-flex justify-content-between align-items-center"
              >
                <span class="fw-semibold">{{ $tipo }}</span>
                <span class="badge rounded-pill text-bg-light">
                  {{ $famiglie->count() }}
                </span>
              </div>
              <ul class="list-group list-group-flush">
                @forelse ($famiglie as $famiglia)
                  <li
                    class="list-group-item d-flex justify-content-between align-items-center gap-2"
                  >
                    <a
                      class="text-truncate"
                      href="{{ route("anagrafe.families.show", $famiglia->id) }}"
                    >
                      {{ $famiglia->nome_famiglia }}
                    </a>
                    <span class="d-flex align-items-center gap-1 flex-shrink-0">
                      @if ($tutteLeFamiglie && $famiglia->in_nomadelfia)
                        <span class="badge bg-success">In Nomadelfia</span>
                      @endif
                      <span
                        class="small text-body-secondary"
                        title="Componenti nel nucleo"
                      >
                        {{ $famiglia->componenti_nel_nucleo }}
                      </span>
                    </span>
                  </li>
                @empty
                  <li class="list-group-item text-body-secondary">
                    Nessuna famiglia
                  </li>
                @endforelse
              </ul>
            </div>
          </div>
        @endforeach
      </div>
    </section>

    <div class="row g-4">
      <section class="col-lg-6" aria-labelledby="titolo-senza-famiglia">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 id="titolo-senza-famiglia" class="h5 mb-0">
            Persone senza famiglia
          </h2>
          @if ($tuttiSingle)
            <a
              class="btn btn-sm profile-btn"
              href="{{ route("anagrafe.families", request()->except("single")) }}"
            >
              Solo in Nomadelfia
            </a>
          @else
            <a
              class="btn btn-sm profile-btn"
              href="{{ route("anagrafe.families", [...request()->query(), "single" => "tutti"]) }}"
            >
              Mostra tutti
            </a>
          @endif
        </div>
        <div class="row">
          @foreach (["Uomini" => $singleMaschio, "Donne" => $singleFemmine] as $titolo => $single)
            <div class="col-md-6">
              <h3 class="h6">
                {{ $titolo }}
                <span class="badge rounded-pill text-bg-light">
                  {{ $single->count() }}
                </span>
              </h3>
              @foreach ($single->get() as $persona)
                <div>
                  <a
                    href="{{ route("nomadelfia.person.show", ["id" => $persona->id]) }}"
                  >
                    {{ $persona->nome_famiglia }}
                  </a>
                  @if ($tuttiSingle && $interni->contains($persona->persona_id))
                    <span class="badge bg-success">In Nomadelfia</span>
                  @endif
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
      </section>

      @if ($famigliaError->isNotEmpty())
        <section class="col-lg-6" aria-labelledby="titolo-da-sistemare">
          <h2 id="titolo-da-sistemare" class="h5 mb-3 text-danger">
            Da sistemare
          </h2>
          @foreach ($famigliaError as $errore)
            <h3 class="h6">
              {{ $errore->descrizione }}
              <span class="badge rounded-pill text-bg-light">
                {{ count($errore->results) }}
              </span>
            </h3>
            <ul>
              @foreach ($errore->results as $famiglia)
                <li>
                  <a
                    href="{{ route("anagrafe.families.show", $famiglia->id) }}"
                  >
                    {{ $famiglia->nome_famiglia }}
                  </a>
                </li>
              @endforeach
            </ul>
          @endforeach
        </section>
      @endif
    </div>
  </div>
@endsection
