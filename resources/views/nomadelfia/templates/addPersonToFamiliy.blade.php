<x-modal
  :modal-id="$modalId ?? null"
  modal-title="Aggiungi come figlio a una famiglia"
  :button-title="$buttonTitle ?? 'Aggiungi come figlio'"
  :button-style="$buttonStyle ?? 'btn-secondary my-2'"
>
  <x-slot:body>
    <form
      method="POST"
      id="formAddPersonToFamily{{ $persona->id }}"
      action="{{ route("anagrafe.person.add-family", $persona->id) }}"
    >
      @csrf
      <livewire:search-famiglia
        name_input="family_id"
        placeholder="---Cerca famiglia---"
      />
      <select class="form-select mt-3" name="type" required>
        <option value="" disabled selected>--- scegli posizione ---</option>
        <option value="FIGLIO NATO">Figlio nato</option>
        <option value="FIGLIO ACCOLTO">Figlio accolto</option>
        <option value="AGGREGATO">Aggregato</option>
      </select>
      <label
        class="form-label mt-3"
        for="data-entrata-famiglia-{{ $persona->id }}"
      >
        Data di ingresso nella famiglia
      </label>
      <input
        class="form-control"
        type="date"
        name="data_entrata"
        id="data-entrata-famiglia-{{ $persona->id }}"
      />
      <div class="form-text">
        Solo se la persona è già nella comunità e cambia famiglia.
      </div>
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button
      class="btn btn-success"
      form="formAddPersonToFamily{{ $persona->id }}"
    >
      Salva
    </button>
  </x-slot:footer>
</x-modal>
