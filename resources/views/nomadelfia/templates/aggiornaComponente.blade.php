<x-modal
  modal-title="Aggiorna componente"
  button-title="Modifica"
  button-style="btn-sm profile-btn"
>
  <x-slot:body>
    <form
      class="form"
      method="POST"
      id="formComponenteAggiorna{{ $componente->id }}"
      action="{{ route("anagrafe.family.member.update", ["id" => $famiglia->id]) }}"
    >
      @csrf
      @method("PUT")
      <div class="row">
        <label for="example-text-input" class="col-4 form-label">
          Persona
        </label>
        <div class="col-8">
          <input
            class="form-control"
            type="text"
            name="persona_id"
            value="{{ $componente->id }}"
            hidden
          />
          {{ $componente->nominativo }}
        </div>
      </div>
      <div class="row">
        <label for="example-text-input" class="col-4 form-label">
          Posizione Famiglia
        </label>
        <div class="col-8">
          <select class="form-select" name="posizione">
            @include("nomadelfia.templates.opzioniPosizione", ["famiglia" => $famiglia, "selezionata" => $componente->pivot->posizione_famiglia])
          </select>
        </div>
      </div>
      <div class="row">
        <label for="example-text-input" class="col-4 form-label">
          Stato:
        </label>
        <div class="col-8">
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="stato"
              id="stato1-{{ $componente->id }}"
              value="1"
              @checked($componente->pivot->stato === "1")
            />
            <label class="form-check-label" for="stato1-{{ $componente->id }}">
              Includi nel nucleo familiare
            </label>
          </div>
          <div class="form-check">
            <input
              class="form-check-input"
              type="radio"
              name="stato"
              id="stato2-{{ $componente->id }}"
              value="0"
              @checked($componente->pivot->stato === "0")
            />
            <label class="form-check-label" for="stato2-{{ $componente->id }}">
              Non includere nel nucleo familiare
            </label>
          </div>
        </div>
      </div>
      <div class="row">
        <label for="example-text-input" class="col-4 form-label"> Note: </label>
        <div class="col-8">
          <textarea
            class="form-control"
            name="note"
            id="note-componente-{{ $componente->id }}"
            rows="3"
          ></textarea>
        </div>
      </div>
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button
      class="btn btn-danger"
      form="formComponenteAggiorna{{ $componente->id }}"
    >
      Salva
    </button>
  </x-slot:footer>
</x-modal>
