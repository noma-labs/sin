<x-modal
  modal-title="Elimina data uscita"
  button-title="Elimina data uscita"
  button-style="btn-outline-danger btn-sm"
>
  <x-slot:body>
    <form
      class="form"
      method="POST"
      id="formEliminaDataUscita{{ $persona->id }}-{{ $periodo->data_uscita }}"
      action="{{ route("nomadelfia.leave.destroy", ["id" => $persona->id, "uscita" => $periodo->data_uscita]) }}"
    >
      @csrf
      @method("DELETE")
      <p class="mb-0">Togliendo la data di uscita del {{ Carbon::parse($periodo->data_uscita)->translatedFormat("j M Y") }}, {{ $persona->nominativo }} risulterà presente in Nomadelfia (popolazione attuale).</p>
      @include("nomadelfia.templates.dateCollegate", ["collegate" => $riaperte, "titolo" => "Verranno riaperte anche:", "nessuna" => "Nessuna posizione o gruppo familiare finisce in questa data: la persona risulterà presente senza posizione né gruppo familiare. Aziende, incarichi e classe restano conclusi."])
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button
      class="btn btn-danger"
      form="formEliminaDataUscita{{ $persona->id }}-{{ $periodo->data_uscita }}"
      type="submit"
    >
      Elimina data uscita
    </button>
  </x-slot:footer>
</x-modal>
