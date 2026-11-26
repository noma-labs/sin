@extends("nomadelfia.index")

@section("title", "Scegli la foto di " . $persona->nome . " " . $persona->cognome)

@section("content")
  @php
    $parametriCoda = $inCoda ? ["coda" => 1] : [];
  @endphp

  <div class="person-profile">
    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
      <x-persona-avatar :persona="$persona" size="lg" />
      <div class="flex-grow-1" style="min-width: 0">
        @if ($inCoda)
          <a class="small" href="{{ route("nomadelfia.person.senza-avatar") }}">
            &larr; Torna alla coda
          </a>
        @else
          <a
            class="small"
            href="{{ route("nomadelfia.person.profile", $persona->id) }}"
          >
            &larr; Torna al profilo
          </a>
        @endif
        <h1 class="profile-name mb-1">
          Scegli la foto di {{ $persona->nome }} {{ $persona->cognome }}
        </h1>
        <p class="text-body-secondary mb-0">Clicca un volto per usarlo come foto, oppure disegna il riquadro intorno a {{ $persona->nome }} su una delle sue foto.</p>
      </div>
      @if ($inCoda)
        @if ($prossima !== null)
          <a
            @class(["btn", "btn-primary" => $persona->avatar_path, "btn-outline-secondary" => ! $persona->avatar_path])
            href="{{ route("nomadelfia.person.avatar.volti", ["id" => $prossima, "coda" => 1]) }}"
          >
            {{ $persona->avatar_path ? "Prossima persona" : "Salta" }} &rarr;
          </a>
        @else
          <a
            class="btn btn-outline-secondary"
            href="{{ route("nomadelfia.person.senza-avatar") }}"
          >
            Fine della coda
          </a>
        @endif
      @endif
    </div>

    @if ($persona->avatar_path)
      <div class="avatar-attuale mb-4">
        <img
          src="{{ $persona->avatarUrl() }}"
          alt="La foto attuale di {{ $persona->nome }} {{ $persona->cognome }}"
          width="256"
          height="256"
        />
        <div>
          <h2 class="h5 mb-1">Foto attuale</h2>
          <p class="text-body-secondary mb-0">
            @if ($persona->avatar_aggiornato_il)
              Salvata il
              {{ $persona->avatar_aggiornato_il->format("d/m/Y") }} alle
              {{ $persona->avatar_aggiornato_il->format("H:i") }}.
            @endif

            Se non va bene, scegli un altro volto o disegna il riquadro qui
            sotto: la nuova foto prende il suo posto.
          </p>
        </div>
      </div>
    @endif

    <h2 class="h5 mb-3">Volti taggati</h2>
    @if ($volti === [])
      <div class="alert alert-light border">
        Nessun volto taggato con il nome FOTO di questa persona.
        @if ($fotoSenzaVolto->isNotEmpty())
          Disegna il riquadro su una delle foto qui sotto.
        @endif
      </div>
    @else
      <div class="avatar-volti mb-4">
        @foreach ($volti as $volto)
          @php
            $dataVolto = $volto->data?->format("d/m/Y");
          @endphp

          <div class="avatar-volto-scheda">
            <form
              method="POST"
              action="{{ route("nomadelfia.person.avatar.volti.store", $persona->id) }}"
            >
              @csrf
              <input
                type="hidden"
                name="photo_id"
                value="{{ $volto->photoId }}"
              />
              <input type="hidden" name="indice" value="{{ $volto->indice }}" />
              @if ($inCoda)
                <input type="hidden" name="coda" value="1" />
              @endif

              <button
                type="submit"
                class="avatar-volto @if ($volto->consigliato) avatar-volto-consigliato @endif"
              >
                <img
                  src="{{ route("nomadelfia.person.avatar.volto", ["id" => $persona->id, "photo" => $volto->photoId, "indice" => $volto->indice]) }}"
                  alt="Usa il volto della foto {{ $dataVolto ? "del " . $dataVolto : "senza data" }}"
                  width="160"
                  height="160"
                  loading="lazy"
                />
                <span class="avatar-volto-info">
                  <span>{{ $dataVolto ?? "senza data" }}</span>
                  @if ($volto->consigliato)
                    <span class="badge text-bg-primary">Consigliata</span>
                  @endif

                  @if ($volto->latoPx < 96)
                    <span class="badge text-bg-warning">Piccola</span>
                  @endif
                </span>
              </button>
            </form>
            <div class="d-flex gap-3 small">
              <a
                href="{{ route("nomadelfia.person.avatar.riquadro", ["id" => $persona->id, "photo" => $volto->photoId, "indice" => $volto->indice] + $parametriCoda) }}"
              >
                Aggiusta
              </a>
              <a href="{{ route("photos.show", $volto->photoId) }}">
                Vedi la foto
              </a>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    @if ($fotoSenzaVolto->isNotEmpty())
      <h2 class="h5 mb-1">Altre foto</h2>
      <p class="text-body-secondary small mb-3">Foto di {{ $persona->nome }} senza il volto taggato: clicca una foto per disegnare il riquadro.</p>
      <div class="avatar-volti">
        @foreach ($fotoSenzaVolto as $foto)
          <a
            class="avatar-volto"
            href="{{ route("nomadelfia.person.avatar.riquadro", ["id" => $persona->id, "photo" => $foto->id] + $parametriCoda) }}"
          >
            <img
              src="{{ route("photos.preview", $foto->id) }}"
              alt="Disegna il riquadro sulla foto {{ $foto->taken_at ? "del " . $foto->taken_at->format("d/m/Y") : "senza data" }}"
              loading="lazy"
            />
            <span class="avatar-volto-info">
              {{ $foto->taken_at?->format("d/m/Y") ?? "senza data" }}
            </span>
          </a>
        @endforeach
      </div>
    @endif
  </div>
@endsection
