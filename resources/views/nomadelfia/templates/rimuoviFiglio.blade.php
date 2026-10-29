<x-modal
  modal-title="Rimuovi dalla famiglia"
  button-title="Rimuovi"
  button-style="btn-sm btn-outline-danger"
>
  <x-slot:body>
    <form
      method="POST"
      id="formRimuoviFiglio{{ $figlio->id }}"
      action="{{ route("anagrafe.family.member.destroy", ["id" => $famiglia->id, "personaId" => $figlio->id]) }}"
    >
      @csrf
      @method("DELETE")
      <p class="mb-2">Rimuovere {{ $figlio->nominativo }} dalla famiglia {{ $famiglia->nome_famiglia }}?</p>
      <p class="text-body-secondary small mb-0">La famiglia non lo elencherà più, neanche tra i figli non più nel nucleo. Per un figlio che ha lasciato la famiglia usa invece Modifica e toglilo dal nucleo.</p>
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button class="btn btn-danger" form="formRimuoviFiglio{{ $figlio->id }}">
      Rimuovi
    </button>
  </x-slot:footer>
</x-modal>
