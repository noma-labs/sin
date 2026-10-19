<x-modal
  modal-title="Aggiungi come figlio a una famiglia"
  button-title="Aggiungi come figlio"
  button-style="btn-secondary my-2"
>
  <x-slot:body>
    <form
      method="POST"
      id="formAddPersonToFamily"
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
      </select>
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button class="btn btn-success" form="formAddPersonToFamily">Salva</button>
  </x-slot:footer>
</x-modal>
