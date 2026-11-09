@extends("scuola.index")

@section("content")
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Argomenti di {{ $elaborato->titolo }}</h1>
    <a
      class="btn btn-outline-secondary btn-sm"
      href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
    >
      Torna all'elaborato
    </a>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          Argomenti ({{ $elaborato->argomenti->count() }})
        </div>
        <ul class="list-group list-group-flush">
          @forelse ($elaborato->argomenti as $argomento)
            <li
              class="list-group-item d-flex justify-content-between align-items-center gap-2"
            >
              <span>
                {{ $argomento->argomento }}
                @if (filled($argomento->materia))
                  <span class="badge text-bg-secondary ms-1">
                    {{ $argomento->materia }}
                  </span>
                @endif
              </span>
              <form
                method="POST"
                action="{{ route("scuola.elaborati.argomenti.destroy", ["id" => $elaborato->id, "argomentoId" => $argomento->id]) }}"
              >
                @csrf
                @method("DELETE")
                <button class="btn btn-outline-danger btn-sm" type="submit">
                  Rimuovi
                </button>
              </form>
            </li>
          @empty
            <li class="list-group-item text-body-secondary">
              Nessun argomento.
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">Aggiungi argomento</div>
        <div class="card-body">
          <form
            method="POST"
            action="{{ route("scuola.elaborati.argomenti.store", $elaborato->id) }}"
          >
            @csrf
            <div class="mb-3">
              <label for="argomento" class="form-label">Argomento</label>
              <input
                class="form-control"
                type="text"
                id="argomento"
                name="argomento"
                list="argomenti-usati"
                maxlength="128"
                value="{{ old("argomento") }}"
                required
              />
              <datalist id="argomenti-usati">
                @foreach ($argomentiUsati as $argomentoUsato)
                  <option value="{{ $argomentoUsato }}"></option>
                @endforeach
              </datalist>
            </div>
            <div class="mb-3">
              <label for="materia" class="form-label">Materia</label>
              <input
                class="form-control"
                type="text"
                id="materia"
                name="materia"
                list="materie-usate"
                maxlength="64"
                value="{{ old("materia") }}"
              />
              <datalist id="materie-usate">
                @foreach ($materieUsate as $materiaUsata)
                  <option value="{{ $materiaUsata }}"></option>
                @endforeach
              </datalist>
            </div>
            <button class="btn btn-success" type="submit">Aggiungi</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
