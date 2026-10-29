@use("App\Anagrafe\Famiglia\Models\TipoFamiglia")

<div class="dropdown">
  <button
    class="btn {{ $buttonStyle ?? "btn-success" }} dropdown-toggle"
    type="button"
    data-bs-toggle="dropdown"
    aria-expanded="false"
  >
    Nuova famiglia
  </button>
  <ul class="dropdown-menu">
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
  </ul>
</div>
