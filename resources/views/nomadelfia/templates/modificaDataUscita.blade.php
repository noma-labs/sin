<x-modal
  modal-title="Modifica data uscita"
  button-title="Modifica data"
  button-style="btn-outline-secondary btn-sm"
>
  <x-slot:body>
    <form
      class="form"
      method="POST"
      id="formModificaDataUscita{{ $persona->id }}-{{ $data_uscita }}"
      action="{{ route("nomadelfia.leave.update", ["id" => $persona->id, "uscita" => $data_uscita]) }}"
    >
      @csrf
      <label class="form-label" for="data_uscita-{{ $data_uscita }}">
        Nuova data uscita
      </label>
      <input
        type="date"
        id="data_uscita-{{ $data_uscita }}"
        name="data_uscita"
        value="{{ $data_uscita }}"
        class="form-control"
        required
      />
      @include("nomadelfia.templates.dateCollegate", ["collegate" => $collegate, "nessuna" => "Nessuna posizione, gruppo familiare, azienda, incarico o classe finisce in questa data: cambia solo la data di uscita."])
    </form>
  </x-slot:body>

  <x-slot:footer>
    <button
      class="btn btn-success"
      form="formModificaDataUscita{{ $persona->id }}-{{ $data_uscita }}"
    >
      Salva
    </button>
  </x-slot:footer>
</x-modal>
