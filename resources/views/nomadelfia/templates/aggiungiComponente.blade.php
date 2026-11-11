<x-modal
  :modal-id="$modalId ?? null"
  modal-title="Aggiungi componente alla famiglia"
  :button-title="$buttonTitle ?? 'Aggiungi Componente'"
  :button-style="$buttonStyle ?? 'btn-primary my-2'"
>
  <x-slot:body>
    <form
      class="form"
      method="POST"
      id="formComponente{{ $modalId ?? $famiglia->id }}"
      action="{{ route("anagrafe.family.member.store", ["id" => $famiglia->id]) }}"
    >
      @csrf
      <div class="row mb-3">
        <label class="col-4 form-label">Persona</label>
        <div class="col-8">
          <livewire:search-persona name_input="persona_id" />
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-4 form-label">Posizione Famiglia</label>
        <div class="col-8">
          <select class="form-select" name="posizione">
            <option value="" selected>---scegli posizione---</option>
            @include("nomadelfia.templates.opzioniPosizione", ["famiglia" => $famiglia])
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-4 form-label">Stato:</label>
        <div class="col-8">
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="stato"
              id="stato1-nuovo-{{ $modalId ?? $famiglia->id }}"
              value="1"
              checked
            />
            <label
              class="form-check-label"
              for="stato1-nuovo-{{ $modalId ?? $famiglia->id }}"
            >
              Includi nel nucleo familiare
            </label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="stato"
              id="stato2-nuovo-{{ $modalId ?? $famiglia->id }}"
              value="0"
            />
            <label
              class="form-check-label"
              for="stato2-nuovo-{{ $modalId ?? $famiglia->id }}"
            >
              Non includere nel nucleo familiare
            </label>
          </div>
        </div>
      </div>
      <div class="row mb-3">
        <label
          class="col-4 form-label"
          for="data-entrata-nuovo-componente-{{ $modalId ?? $famiglia->id }}"
        >
          Data di ingresso
        </label>
        <div class="col-8">
          <input
            class="form-control"
            type="date"
            name="data_entrata"
            id="data-entrata-nuovo-componente-{{ $modalId ?? $famiglia->id }}"
          />
          <div class="form-text">
            Solo per un figlio accolto o aggregato che cambia famiglia quando è
            già nella comunità.
          </div>
        </div>
      </div>
      <div class="row">
        <label class="col-4 form-label">Note:</label>
        <div class="col-8">
          <textarea
            class="form-control"
            name="note"
            id="note-nuovo-componente-{{ $modalId ?? $famiglia->id }}"
            rows="3"
          ></textarea>
        </div>
      </div>
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button
      class="btn btn-danger"
      form="formComponente{{ $modalId ?? $famiglia->id }}"
    >
      Salva
    </button>
  </x-slot:footer>
</x-modal>
