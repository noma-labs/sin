{{--
  The modals of the Parentele menu to give the persona a stato familiare that does not come from the famiglia,
  back to the profile after saving: vedovo when the coniuge is not in the anagrafe, separato and sacerdote.
--}}
@php
  $spiegazioni = [
      "VED" => "Solo se il coniuge non è registrato: altrimenti lo stato di vedovo deriva dal decesso del coniuge.",
      "SEP" => "Dalla separazione dal coniuge.",
      "SAC" => "Dall'ordinazione sacerdotale.",
  ];
@endphp

@foreach ($statiFamiliari as $stato)
  @continue($statiFamiliariAttivi->contains($stato->id))
  @php
    $modalId = "modal-stato-" . mb_strtolower($stato->stato);
  @endphp

  <x-modal
    :modal-id="$modalId"
    :modal-title="'Stato familiare: ' . $etichettaStato($stato)"
  >
    <x-slot:body>
      <form
        method="POST"
        id="form-{{ $modalId }}"
        action="{{ route("nomadelfia.persone.stato.assegna", $persona->id) }}"
      >
        @csrf
        <input type="hidden" name="torna_indietro" value="1" />
        <input type="hidden" name="stato_id" value="{{ $stato->id }}" />
        <p class="text-body-secondary">
          {{ $persona->nome }} è
          <strong>{{ mb_strtolower($etichettaStato($stato)) }}</strong>
          da questa data. {{ $spiegazioni[$stato->stato] ?? "" }}
        </p>
        <label class="form-label" for="{{ $modalId }}-data-inizio">
          Data di inizio
        </label>
        <input
          class="form-control"
          type="date"
          name="data_inizio"
          id="{{ $modalId }}-data-inizio"
          required
        />
      </form>
    </x-slot:body>
    <x-slot:footer>
      <button
        type="button"
        class="btn btn-outline-secondary"
        data-bs-dismiss="modal"
      >
        Annulla
      </button>
      <button class="btn btn-success" form="form-{{ $modalId }}">Salva</button>
    </x-slot:footer>
  </x-modal>
@endforeach
