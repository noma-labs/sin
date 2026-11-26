@extends("nomadelfia.index")

@section("title", "Disegna il riquadro di " . $persona->nome . " " . $persona->cognome)

@section("content")
  @php
    $torna = route("nomadelfia.person.avatar.volti", ["id" => $persona->id] + ($inCoda ? ["coda" => 1] : []));
  @endphp

  <div class="person-profile">
    <div class="mb-3">
      <a class="small" href="{{ $torna }}">&larr; Torna ai volti</a>
      <h1 class="profile-name mb-1">
        Disegna il riquadro di {{ $persona->nome }} {{ $persona->cognome }}
      </h1>
      <p class="text-body-secondary mb-0">Trascina sulla foto per disegnare un quadrato intorno al viso, trascinalo per spostarlo e usa l'angolo in basso a destra per ingrandirlo. Con il riquadro selezionato le frecce lo spostano, con Maiusc lo ingrandiscono.</p>
    </div>

    <div class="d-flex flex-wrap align-items-start gap-4">
      <div
        class="avatar-riquadro"
        data-avatar-riquadro="form-riquadro"
        data-iniziale="{{ $iniziale ? json_encode($iniziale) : "" }}"
      >
        <img
          src="{{ route("photos.preview", $foto->id) }}"
          alt="Foto {{ $foto->taken_at ? "del " . $foto->taken_at->format("d/m/Y") : "senza data" }} di {{ $persona->nome }}"
          draggable="false"
        />
        <div
          class="avatar-riquadro-box"
          data-riquadro-box
          tabindex="0"
          aria-label="Riquadro da ritagliare: frecce per spostarlo, Maiusc e frecce per ingrandirlo"
          hidden
        >
          <span class="avatar-riquadro-maniglia" data-riquadro-maniglia></span>
        </div>
      </div>

      <form
        method="POST"
        id="form-riquadro"
        class="d-flex flex-column align-items-center gap-3"
        action="{{ route("nomadelfia.person.avatar.riquadro.store", ["id" => $persona->id, "photo" => $foto->id]) }}"
      >
        @csrf
        <input type="hidden" name="x" />
        <input type="hidden" name="y" />
        <input type="hidden" name="w" />
        <input type="hidden" name="h" />
        @if ($inCoda)
          <input type="hidden" name="coda" value="1" />
        @endif

        <div
          class="avatar-riquadro-anteprima"
          data-riquadro-anteprima
          role="img"
          aria-label="Anteprima della foto"
        ></div>
        <button type="submit" class="btn btn-primary">Usa come foto</button>
        <a class="small" href="{{ $torna }}">Annulla</a>
      </form>
    </div>
  </div>
@endsection
