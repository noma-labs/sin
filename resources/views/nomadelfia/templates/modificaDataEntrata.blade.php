<x-modal
  modal-title="Modifica data entrata"
  button-title="Modifica data"
  button-style="btn-outline-secondary btn-sm"
>
  <x-slot:body>
    <form
      class="form"
      method="POST"
      id="formModificaDataEntrata{{ $persona->id }}-{{ $data_entrata }}"
      action="{{ route("nomadelfia.join.update", ["id" => $persona->id, "entrata" => $data_entrata]) }}"
    >
      @csrf
      @method("PUT")
      <label class="form-label" for="data_entrata-{{ $data_entrata }}">
        Nuova data entrata
      </label>
      <input
        type="date"
        id="data_entrata-{{ $data_entrata }}"
        name="data_entrata"
        value="{{ $data_entrata }}"
        class="form-control"
        required
      />
      @include("nomadelfia.templates.dateCollegate", ["collegate" => $collegate, "nessuna" => "Nessuna posizione o gruppo familiare inizia in questa data: cambia solo la data di entrata."])
    </form>
  </x-slot:body>

  <x-slot:footer>
    <button
      class="btn btn-success"
      form="formModificaDataEntrata{{ $persona->id }}-{{ $data_entrata }}"
    >
      Salva
    </button>
  </x-slot:footer>
</x-modal>
