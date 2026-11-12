{{-- The menu of the profile to give the persona a posizione, to put or move her in a gruppo familiare, to add an azienda or an incarico, and a past period in Nomadelfia --}}
<div class="dropdown">
  <button
    class="btn btn-sm profile-btn rounded-pill dropdown-toggle"
    type="button"
    data-bs-toggle="dropdown"
    data-bs-popper-config='{"strategy":"fixed"}'
    aria-expanded="false"
  >
    Aggiungi
  </button>
  <ul class="dropdown-menu">
    @can("popolazione.persona.inserisci")
      <li><h6 class="dropdown-header">Posizione</h6></li>
      @foreach ($posizioniComunita as $posizione)
        <li>
          @if ($posizione->id === $posizioneAttuale?->id)
            <span class="dropdown-item disabled" aria-disabled="true">
              {{ $posizione->nome }}
              <small>&middot; attuale</small>
            </span>
          @else
            <button
              type="button"
              class="dropdown-item"
              data-bs-toggle="modal"
              data-bs-target="#modal-posizione-{{ mb_strtolower($posizione->abbreviato) }}"
            >
              {{ $posizione->nome }}
            </button>
          @endif
        </li>
      @endforeach

      <li><hr class="dropdown-divider" /></li>
      <li><h6 class="dropdown-header">Gruppo familiare</h6></li>
      <li>
        <button
          type="button"
          class="dropdown-item"
          data-bs-toggle="modal"
          data-bs-target="#modal-gruppo-familiare"
        >
          {{ $gruppoAttuale ? "Sposta in un altro gruppo" : "Aggiungi a un gruppo" }}
        </button>
      </li>
    @endcan

    <li><hr class="dropdown-divider" /></li>
    <li><h6 class="dropdown-header">Lavoro</h6></li>
    @can("popolazione.persona.modifica")
      <li>
        <button
          type="button"
          class="dropdown-item"
          data-bs-toggle="modal"
          data-bs-target="#modal-azienda"
        >
          Azienda
        </button>
      </li>
    @endcan

    @can("popolazione.persona.inserisci")
      <li>
        <button
          type="button"
          class="dropdown-item"
          data-bs-toggle="modal"
          data-bs-target="#modal-incarico"
        >
          Incarico
        </button>
      </li>
    @endcan

    @can("popolazione.persona.inserisci")
      <li><hr class="dropdown-divider" /></li>
      <li><h6 class="dropdown-header">Entrate e uscite</h6></li>
      <li>
        <button
          type="button"
          class="dropdown-item"
          data-bs-toggle="modal"
          data-bs-target="#modal-periodo-storico"
        >
          Periodo storico
        </button>
      </li>
    @endcan
  </ul>
</div>
