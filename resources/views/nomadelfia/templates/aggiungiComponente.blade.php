<x-modal
  modal-title="Aggiungi componente alla famiglia"
  button-title="Aggiungi Componente"
  :button-style="$buttonStyle ?? 'btn-primary my-2'"
>
  <x-slot:body>
    <form
      class="form"
      method="POST"
      id="formComponente"
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
              id="stato1-nuovo"
              value="1"
              checked
            />
            <label class="form-check-label" for="stato1-nuovo">
              Includi nel nucleo familiare
            </label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="stato"
              id="stato2-nuovo"
              value="0"
            />
            <label class="form-check-label" for="stato2-nuovo">
              Non includere nel nucleo familiare
            </label>
          </div>
        </div>
      </div>
      <div class="row">
        <label class="col-4 form-label">Note:</label>
        <div class="col-8">
          <textarea
            class="form-control"
            name="note"
            id="note-nuovo-componente"
            rows="3"
          ></textarea>
        </div>
      </div>
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button class="btn btn-danger" form="formComponente">Salva</button>
  </x-slot:footer>
</x-modal>
