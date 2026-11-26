@extends("scuola.index")

@section("title", $elaborato->titolo)

@section("content")
  <div class="row g-3">
    <div class="col-lg-4 d-flex flex-column gap-3">
      <div class="card">
        <div
          class="card-header d-flex justify-content-between align-items-center"
        >
          <span>Scheda</span>
          <a
            href="{{ route("scuola.elaborati.edit", $elaborato->id) }}"
            class="btn btn-warning btn-sm"
          >
            Modifica
          </a>
        </div>
        <div class="card-body">
          <h1 class="h5 mb-3">{{ $elaborato->titolo }}</h1>
          <div class="d-flex gap-3">
            <div class="flex-shrink-0 text-center" style="width: 100px">
              <img
                src="{{ $elaborato->cover_image_path ? route("scuola.elaborati.cover.show", $elaborato->id) : asset("images/placeholder.svg") }}"
                class="img-fluid mb-1"
                alt="Copertina di {{ $elaborato->titolo }}"
              />
              <x-modal
                modal-id="modal-copertina"
                button-style="btn-link btn-sm p-0"
                button-title="{{ $elaborato->cover_image_path ? 'Cambia copertina' : 'Carica copertina' }}"
                modal-title="Copertina di {{ $elaborato->titolo }}"
              >
                <x-slot:body>
                  <form
                    method="POST"
                    id="form-copertina"
                    action="{{ route("scuola.elaborati.cover.store", $elaborato->id) }}"
                    enctype="multipart/form-data"
                    class="text-start"
                  >
                    @csrf
                    <label class="form-label" for="copertina">
                      {{ $elaborato->cover_image_path ? "Sostituisci con una nuova copertina" : "Scegli la copertina" }}
                    </label>
                    <input
                      id="copertina"
                      type="file"
                      class="form-control"
                      name="file"
                      accept="image/jpeg,image/png"
                      required
                    />
                    <div class="form-text">
                      JPG o PNG fino a 10 MB, anche la scansione così com'è:
                      viene convertita in PNG automaticamente.
                    </div>
                  </form>
                </x-slot:body>
                <x-slot:footer>
                  <button
                    type="submit"
                    class="btn btn-primary"
                    form="form-copertina"
                  >
                    Salva
                  </button>
                </x-slot:footer>
              </x-modal>
            </div>
            <dl class="flex-grow-1 mb-0">
              @foreach ([
                  "Collocazione" => $elaborato->collocazione,
                  "Anno scolastico" => $elaborato->anno_scolastico,
                  "Classi" => str_replace(",", ", ", (string) $elaborato->classi),
                  "Dimensione" => $elaborato->dimensione,
                  "Rilegatura" => $elaborato->rilegatura,
                  "Autore" => $elaborato->autore,
                ]
                as $etichetta => $valore)
                <dt class="small text-body-secondary fw-normal">
                  {{ $etichetta }}
                </dt>
                <dd class="mb-2">{{ filled($valore) ? $valore : "—" }}</dd>
              @endforeach

              @if ($elaborato->libro_id)
                @can("biblioteca.libro.prenota")
                  <dt class="small text-body-secondary fw-normal">Libro</dt>
                  <dd class="mb-0">
                    <a href="{{ route("books.show", $elaborato->libro_id) }}">
                      Vedi scheda
                    </a>
                  </dd>
                @endcan
              @endif
            </dl>
          </div>
          <hr />
          <div class="small text-body-secondary">Note</div>
          <p class="mb-0">{{ filled($elaborato->note) ? $elaborato->note : "—" }}</p>
        </div>
      </div>

      <div class="card">
        <div
          class="card-header d-flex justify-content-between align-items-center"
        >
          <span>Studenti ({{ $elaborato->studenti->count() }})</span>
          <a
            class="btn btn-primary btn-sm"
            href="{{ route("scuola.elaborati.students.create", $elaborato->id) }}"
            role="button"
          >
            Gestisci
          </a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse ($elaborato->studenti as $studente)
            <li class="list-group-item d-flex align-items-center gap-2">
              <x-persona-avatar :persona="$studente" size="sm" />
              @include("scuola.templates.student", ["persona" => $studente])
            </li>
          @empty
            <li class="list-group-item text-body-secondary">
              Nessuno studente.
            </li>
          @endforelse
        </ul>
      </div>

      <div class="card">
        <div class="card-header">
          Coordinatori ({{ $elaborato->coordinatori->count() }})
        </div>
        <ul class="list-group list-group-flush">
          @forelse ($elaborato->coordinatori as $coordinatore)
            <li class="list-group-item">
              @include("nomadelfia.templates.persona", ["persona" => $coordinatore])
            </li>
          @empty
            <li class="list-group-item text-body-secondary">
              Nessun coordinatore.
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        @if (empty($elaborato->file_path))
          <div class="card-header">Documento</div>
          <div class="card-body">
            <p class="text-body-secondary">Nessun file digitale caricato.</p>
            <form
              method="POST"
              action="{{ route("scuola.elaborati.media.store", ["id" => $elaborato->id]) }}"
              enctype="multipart/form-data"
            >
              @csrf
              <div class="mb-3">
                <label for="file" class="form-label">Scegli file</label>
                <input
                  type="file"
                  id="file"
                  name="file"
                  class="form-control"
                  required
                />
              </div>
              <button class="btn btn-success" type="submit">Carica</button>
            </form>
          </div>
        @else
          <div
            class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2"
          >
            <span>
              Documento
              <small class="text-body-secondary">
                {{ collect([$elaborato->file_mime_type, $elaborato->file_size ? Illuminate\Support\Number::fileSize($elaborato->file_size) : null])->filter()->implode(" · ") }}
              </small>
            </span>
            <span class="d-flex gap-2">
              <a
                href="{{ route("scuola.elaborati.preview", $elaborato->id) }}"
                class="btn btn-outline-primary btn-sm"
                target="_blank"
                rel="noopener"
              >
                Apri
              </a>
              <a
                href="{{ route("scuola.elaborati.download", $elaborato->id) }}"
                class="btn btn-primary btn-sm"
              >
                Scarica
              </a>
              <x-modal
                modal-title="Elimina file"
                button-title="Elimina"
                button-style="btn-outline-danger btn-sm"
              >
                <x-slot:body>
                  <form
                    method="POST"
                    id="formEliminaFileElaborato{{ $elaborato->id }}"
                    action="{{ route("scuola.elaborati.media.destroy", $elaborato->id) }}"
                  >
                    @csrf
                    @method("DELETE")
                    <p>Vuoi davvero eliminare il file di
                    <strong>{{ $elaborato->titolo }}</strong>
                    ? Il file verrà rimosso dal disco e non potrà essere recuperato.</p>
                  </form>
                </x-slot:body>
                <x-slot:footer>
                  <button
                    class="btn btn-danger btn-sm"
                    form="formEliminaFileElaborato{{ $elaborato->id }}"
                  >
                    Elimina
                  </button>
                </x-slot:footer>
              </x-modal>
            </span>
          </div>
          <iframe
            src="{{ route("scuola.elaborati.preview", $elaborato->id) }}"
            class="d-block w-100 border-0"
            style="height: 85vh"
            title="Anteprima di {{ $elaborato->titolo }}"
          ></iframe>
        @endif
      </div>
    </div>
  </div>
@endsection
