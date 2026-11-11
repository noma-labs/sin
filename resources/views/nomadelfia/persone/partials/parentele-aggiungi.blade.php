@use("App\Anagrafe\Famiglia\Models\TipoFamiglia")

{{-- The menu to add a parente of the persona in the profile: coniuge, figli and fratelli, a nuova famiglia, or the persona as figlio of a famiglia --}}
<div class="dropdown">
  <button
    class="{{ $stileBottone }} dropdown-toggle"
    type="button"
    data-bs-toggle="dropdown"
    data-bs-popper-config='{"strategy":"fixed"}'
    aria-expanded="false"
  >
    @if ($conIcona ?? false)
      <span class="profile-person-avatar profile-person-piu" aria-hidden="true">
        +
      </span>
    @endif

    {{ $titoloBottone }}
  </button>
  <ul class="dropdown-menu dropdown-menu-end">
    <li><h6 class="dropdown-header">Aggiungi un parente</h6></li>
    @foreach ($azioniParentele as $azione)
      <li>
        @if ($azione["modal"])
          <button
            type="button"
            class="dropdown-item"
            data-bs-toggle="modal"
            data-bs-target="#{{ $azione["modal"] }}"
          >
            {{ $azione["etichetta"] }}
          </button>
        @elseif ($azione["href"])
          <a class="dropdown-item" href="{{ $azione["href"] }}">
            {{ $azione["etichetta"] }}
          </a>
        @else
          <span class="dropdown-item disabled" aria-disabled="true">
            {{ $azione["etichetta"] }}
            @if ($azione["nota"])
              <small>&middot; {{ $azione["nota"] }}</small>
            @endif
          </span>
        @endif
      </li>
    @endforeach

    @can("popolazione.persona.inserisci")
      <li><hr class="dropdown-divider" /></li>
      <li><h6 class="dropdown-header">Nuova famiglia</h6></li>
      @foreach (TipoFamiglia::perSesso($persona->sesso) as $tipo)
        <li>
          <a
            class="dropdown-item"
            href="{{ route("anagrafe.families.create", ["persona" => $persona->id, "tipo" => $tipo->value]) }}"
          >
            @if ($tipo === TipoFamiglia::Coniugi)
              Coniugi ({{ $persona->isMaschio() ? "come marito" : "come moglie" }})
            @else
              {{ $tipo->nome() }}
            @endif
          </a>
        </li>
      @endforeach

      @if ($puoAggiungereComeFiglio)
        <li><hr class="dropdown-divider" /></li>
        <li>
          <button
            type="button"
            class="dropdown-item"
            data-bs-toggle="modal"
            data-bs-target="#modal-aggiungi-come-figlio"
          >
            Aggiungi come figlio a una famiglia
          </button>
        </li>
      @endif
    @endcan
  </ul>
</div>
