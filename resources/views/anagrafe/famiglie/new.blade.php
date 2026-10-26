@extends("nomadelfia.index")

@section("content")
  @use("App\Anagrafe\Famiglia\Models\TipoFamiglia")

  <form
    class="person-profile"
    method="POST"
    action="{{ route("anagrafe.families.store") }}"
    id="form-nuova-famiglia"
  >
    @csrf
    <header class="profile-band profile-band-solo rounded-3 mb-4">
      <div class="d-flex flex-wrap gap-3 align-items-start">
        <div class="profile-avatar" aria-hidden="true">
          <svg
            width="44"
            height="44"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M3 10.5 12 3l9 7.5" />
            <path d="M5 9.5V21h14V9.5" />
            <path d="M10 21v-6h4v6" />
          </svg>
        </div>
        <div class="flex-grow-1" style="min-width: 0">
          <h1 class="profile-name mb-0">Nuova famiglia</h1>
          <div class="profile-muted">
            Le persone possono essere in popolazione o solo in anagrafe. Nessuno
            entra o esce dalla popolazione.
          </div>
        </div>
      </div>
    </header>

    <div class="row g-4">
      <div class="col-12 d-flex flex-column gap-4">
        <section aria-labelledby="titolo-tipo">
          <h2 id="titolo-tipo" class="h5 mb-3">Tipo di famiglia</h2>
          <div class="row row-cols-2 row-cols-md-3 row-cols-xxl-5 g-2">
            @foreach (TipoFamiglia::cases() as $caso)
              <div class="col">
                <input
                  class="btn-check"
                  type="radio"
                  name="tipo"
                  id="tipo-{{ $caso->value }}"
                  value="{{ $caso->value }}"
                  data-etichetta-data="{{ $caso->etichettaDataCreazione() }}"
                  data-etichetta-capofamiglia="{{ $caso === TipoFamiglia::Coniugi ? "Marito" : $caso->nome() }}"
                  @checked($caso === $tipo)
                />
                <label class="profile-tipo" for="tipo-{{ $caso->value }}">
                  <span class="fw-semibold">{{ $caso->nome() }}</span>
                  <small>{{ $caso->descrizione() }}</small>
                  <span class="profile-tipo-data">
                    {{ $caso->etichettaDataCreazione() }}
                  </span>
                </label>
              </div>
            @endforeach
          </div>
          @error("tipo")
            <div class="text-danger small mt-2">{{ $message }}</div>
          @enderror
        </section>

        <section aria-labelledby="titolo-data">
          <h2 id="titolo-data" class="h5 mb-3" data-etichetta-data>
            {{ $tipo->etichettaDataCreazione() }}
          </h2>
          <div class="profile-kv card mb-0">
            <div class="profile-kv-row">
              <dt>
                <label for="data_creazione" data-etichetta-data>
                  {{ $tipo->etichettaDataCreazione() }}
                </label>
              </dt>
              <dd>
                <input
                  class="form-control @error("data_creazione") is-invalid @enderror"
                  style="max-width: 14rem"
                  type="date"
                  id="data_creazione"
                  name="data_creazione"
                  value="{{ old("data_creazione") }}"
                  aria-describedby="aiuto-data"
                />
                @error("data_creazione")
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Nome famiglia</dt>
              <dd>
                <label class="visually-hidden" for="nome_famiglia">
                  Nome famiglia
                </label>
                <input
                  class="form-control"
                  type="text"
                  id="nome_famiglia"
                  name="nome_famiglia"
                  value="{{ old("nome_famiglia") }}"
                  placeholder="Il nominativo del capofamiglia"
                />
              </dd>
              <span></span>
            </div>
          </div>
          <div id="aiuto-data" class="text-body-secondary small mt-2">
            <span data-solo-coniugi>
              Obbligatoria: diventa la data di creazione della famiglia.
            </span>
            <span data-senza-coniuge>
              Facoltativa: lasciala vuota se non è conosciuta.
            </span>
          </div>
        </section>

        <section aria-labelledby="titolo-genitori">
          <h2 id="titolo-genitori" class="h5 mb-3">Genitori</h2>
          <div class="profile-kv card mb-0">
            <div class="profile-kv-row">
              <dt data-etichetta-capofamiglia>
                {{ $tipo === TipoFamiglia::Coniugi ? "Marito" : $tipo->nome() }}
              </dt>
              <dd>
                <livewire:search-persona
                  name_input="capofamiglia"
                  :persone_id="$capofamigliaId"
                  placeholder="Cerca in anagrafe"
                />
                @error("capofamiglia")
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
              </dd>
              <span></span>
            </div>
            <div class="profile-kv-row" data-solo-coniugi>
              <dt>Moglie</dt>
              <dd>
                <livewire:search-persona
                  name_input="moglie"
                  :persone_id="$moglieId"
                  placeholder="Cerca in anagrafe"
                />
                @error("moglie")
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
              </dd>
              <span></span>
            </div>
          </div>
          <div class="text-body-secondary small mt-2">
            La ricerca include tutta l'anagrafe, anche chi non è in comunità. I
            genitori lasciano la famiglia in cui sono ora. Non è in anagrafe?
            <a href="{{ route("anagrafe.person.create") }}" target="_blank">
              Inserisci la persona
            </a>
            e poi cercala.
          </div>
        </section>

        <section aria-labelledby="titolo-figli">
          <h2 id="titolo-figli" class="h5 mb-3">Figli</h2>
          <div class="profile-kv card mb-0">
            <div class="profile-kv-row">
              <dt>Figli nati</dt>
              <dd>
                <livewire:search-persona
                  name_input="figli_nati[]"
                  :persone_id="$figliNatiId"
                  :multiple="true"
                  placeholder="Cerca in anagrafe"
                />
                @error("figli_nati.*")
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
              </dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Figli accolti</dt>
              <dd>
                <livewire:search-persona
                  name_input="figli_accolti[]"
                  :persone_id="$figliAccoltiId"
                  :multiple="true"
                  placeholder="Cerca in anagrafe"
                />
                @error("figli_accolti.*")
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
              </dd>
              <span></span>
            </div>
          </div>

          <div class="text-body-secondary small">
            La ricerca include tutta l'anagrafe, anche chi non è in comunità. I
            genitori lasciano la famiglia in cui sono ora. Non è in anagrafe?
            <a href="{{ route("anagrafe.person.create") }}" target="_blank">
              Inserisci la persona
            </a>
            e poi cercala.
          </div>
        </section>

        <div class="d-flex flex-wrap justify-content-end gap-2">
          <a class="btn profile-btn" href="{{ route("anagrafe.families") }}">
            Annulla
          </a>
          <button class="btn btn-success" type="submit">Crea famiglia</button>
        </div>
      </div>
    </div>
  </form>

  <script>
    (function () {
      const form = document.getElementById("form-nuova-famiglia");
      const dataCreazione = document.getElementById("data_creazione");

      function aggiornaTipo() {
        const tipo = form.querySelector('input[name="tipo"]:checked');
        const isConiugi = tipo.value === "CON";
        form.querySelectorAll("[data-etichetta-data]").forEach(function (el) {
          el.textContent = tipo.dataset.etichettaData;
        });
        form
          .querySelectorAll("[data-etichetta-capofamiglia]")
          .forEach(function (el) {
            el.textContent = tipo.dataset.etichettaCapofamiglia;
          });
        form.querySelectorAll("[data-solo-coniugi]").forEach(function (el) {
          el.hidden = !isConiugi;
        });
        form.querySelectorAll("[data-senza-coniuge]").forEach(function (el) {
          el.hidden = isConiugi;
        });
        dataCreazione.required = isConiugi;
      }

      form.querySelectorAll('input[name="tipo"]').forEach(function (radio) {
        radio.addEventListener("change", aggiornaTipo);
      });
      aggiornaTipo();
    })();
  </script>
@endsection
