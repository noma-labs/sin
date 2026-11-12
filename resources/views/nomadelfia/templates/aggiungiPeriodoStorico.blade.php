{{-- The modal of the "Aggiungi" menu of the profile to record a past period of the persona in Nomadelfia --}}
@php
  $modalId = "modal-periodo-storico";
@endphp

<x-modal :modal-id="$modalId" modal-title="Aggiungi periodo storico">
  <x-slot:body>
    <form
      class="form"
      method="POST"
      id="form-{{ $modalId }}"
      action="{{ route("nomadelfia.join-leave-history.store", $persona->id) }}"
    >
      @csrf
      <p>Registra un periodo passato in cui {{ $persona->nominativo }} ha vissuto in Nomadelfia.</p>
      <div class="mb-3">
        <label class="form-label" for="{{ $modalId }}-data-entrata">
          Data entrata
        </label>
        <input
          type="date"
          id="{{ $modalId }}-data-entrata"
          name="data_entrata"
          value="{{ old("data_entrata") }}"
          class="form-control"
          required
        />
      </div>
      <div class="mb-3">
        <label class="form-label" for="{{ $modalId }}-data-uscita">
          Data uscita
          @unless ($persona->isDeceduta())
            <span class="text-body-secondary">(facoltativa)</span>
          @endunless
        </label>
        <input
          type="date"
          id="{{ $modalId }}-data-uscita"
          name="data_uscita"
          value="{{ old("data_uscita") }}"
          class="form-control"
          aria-describedby="{{ $modalId }}-data-uscita-nota"
          @required($persona->isDeceduta())
        />
        @unless ($persona->isDeceduta())
          <div
            id="{{ $modalId }}-data-uscita-nota"
            class="form-text text-warning-emphasis"
          >
            Se la data di uscita non è nota, lasciala vuota. Una persona senza
            data di uscita è considerata nella popolazione attuale: è possibile
            solo se il periodo inserito è successivo all'ultima data di uscita.
          </div>
        @endunless
      </div>
      <small class="form-text text-muted">
        Posizioni, gruppi familiari e famiglie della persona non vengono
        modificati.
      </small>
    </form>
  </x-slot:body>
  <x-slot:footer>
    <button class="btn btn-success" form="form-{{ $modalId }}">Salva</button>
  </x-slot:footer>
</x-modal>
