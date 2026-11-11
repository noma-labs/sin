<x-modal
  modal-title="Elimina periodo"
  button-title="Elimina periodo"
  button-style="btn-outline-danger btn-sm"
>
  <x-slot:body>
    <form
      class="form"
      method="POST"
      id="formEliminaPeriodo{{ $persona->id }}-{{ $periodo->data_entrata }}"
      action="{{ route("nomadelfia.join-leave-history.destroy", ["id" => $persona->id, "entrata" => $periodo->data_entrata]) }}"
    >
      @csrf
      @method("DELETE")
      <p class="mb-0">Vuoi davvero eliminare il periodo in Nomadelfia di {{ $persona->nominativo }} dal {{ Carbon::parse($periodo->data_entrata)->translatedFormat("j M Y") }} al {{ Carbon::parse($periodo->data_uscita)->translatedFormat("j M Y") }}?</p>
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button
      class="btn btn-danger"
      form="formEliminaPeriodo{{ $persona->id }}-{{ $periodo->data_entrata }}"
      type="submit"
    >
      Elimina
    </button>
  </x-slot:footer>
</x-modal>
