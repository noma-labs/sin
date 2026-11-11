{{--
  A modal of the profile to add a parente to a famiglia, back to the profile after saving:
  a figlio or a fratello, choosing how he enters the famiglia ($posizioni: posizione => [nome, descrizione]),
  or the coniuge, with the posizione already known ($posizione).
--}}
<x-modal :modal-id="$modalId" :modal-title="$titolo">
  <x-slot:body>
    <form
      method="POST"
      id="form-{{ $modalId }}"
      action="{{ route("anagrafe.family.member.store", ["id" => $famiglia->id]) }}"
    >
      @csrf
      <input type="hidden" name="stato" value="1" />
      <input type="hidden" name="torna_indietro" value="1" />
      <p class="text-body-secondary">{{ $descrizione }}</p>

      <div class="mb-3">
        <label class="form-label">{{ $etichettaPersona }}</label>
        <livewire:search-persona name_input="persona_id" />
      </div>

      @isset($posizione)
        <input type="hidden" name="posizione" value="{{ $posizione }}" />
      @else
        <fieldset class="mb-3">
          <legend class="form-label fs-6">Come entra nella famiglia</legend>
          <div class="row row-cols-1 row-cols-sm-3 g-2">
            @foreach ($posizioni as $valore => [$nome, $spiegazione])
              <div class="col">
                <input
                  class="btn-check"
                  type="radio"
                  name="posizione"
                  id="{{ $modalId }}-{{ Illuminate\Support\Str::slug($valore) }}"
                  value="{{ $valore }}"
                  @checked($loop->first)
                />
                <label
                  class="profile-tipo"
                  for="{{ $modalId }}-{{ Illuminate\Support\Str::slug($valore) }}"
                >
                  <span class="fw-semibold">{{ $nome }}</span>
                  <small>{{ $spiegazione }}</small>
                </label>
              </div>
            @endforeach
          </div>
        </fieldset>

        <label class="form-label" for="{{ $modalId }}-data-entrata">
          Data di entrata nella famiglia
          <span class="text-body-secondary">(facoltativa)</span>
        </label>
        <input
          class="form-control"
          type="date"
          name="data_entrata"
          id="{{ $modalId }}-data-entrata"
        />
        <div class="form-text">
          Solo per chi è accolto o aggregato quando è già nella comunità.
        </div>
      @endisset
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button
      type="button"
      class="btn btn-outline-secondary"
      data-bs-dismiss="modal"
    >
      Annulla
    </button>
    <button class="btn btn-success" form="form-{{ $modalId }}">
      {{ $conferma }}
    </button>
  </x-slot:footer>
</x-modal>
