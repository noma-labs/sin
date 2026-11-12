{{--
  The modals of the "Aggiungi" menu of the profile, back to the profile after saving:
  a new posizione, the entrata in a gruppo familiare (or the move to another one), an azienda, an incarico and a past period in Nomadelfia.
--}}
@php
  $oggi = now()->toDateString();
@endphp

@can("popolazione.persona.inserisci")
  @foreach ($posizioniComunita as $posizione)
    @continue($posizione->id === $posizioneAttuale?->id)
    @php
      $modalId = "modal-posizione-" . mb_strtolower($posizione->abbreviato);
    @endphp

    <x-modal
      :modal-id="$modalId"
      :modal-title="'Posizione: ' . $posizione->nome"
    >
      <x-slot:body>
        <form
          method="POST"
          id="form-{{ $modalId }}"
          action="{{ route("nomadelfia.person.position.store", $persona->id) }}"
        >
          @csrf
          <input type="hidden" name="torna_indietro" value="1" />
          <input
            type="hidden"
            name="posizione_id"
            value="{{ $posizione->id }}"
          />
          <p class="text-body-secondary">
            {{ $persona->nome }} ha la posizione
            <strong>{{ $posizione->nome }}</strong>
            nella comunità da questa data.
            @if ($posizioneAttuale)
              La posizione attuale
              <strong>{{ $posizioneAttuale->nome }}</strong>
              si conclude lo stesso giorno.
            @endif
          </p>
          <label class="form-label" for="{{ $modalId }}-data-inizio">
            Data di inizio
          </label>
          <input
            class="form-control"
            type="date"
            name="data_inizio"
            id="{{ $modalId }}-data-inizio"
            value="{{ $oggi }}"
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
        <button class="btn btn-success" form="form-{{ $modalId }}">
          Salva
        </button>
      </x-slot:footer>
    </x-modal>
  @endforeach

  @include("nomadelfia.templates.aggiungiPeriodoStorico")

  <x-modal
    modal-id="modal-gruppo-familiare"
    :modal-title="$gruppoAttuale ? 'Sposta in un altro gruppo familiare' : 'Aggiungi a un gruppo familiare'"
  >
    <x-slot:body>
      <form
        method="POST"
        id="form-modal-gruppo-familiare"
        action="{{ $gruppoAttuale ? route("nomadelfia.persone.gruppo.sposta", ["id" => $persona->id, "idGruppo" => $gruppoAttuale->id]) : route("nomadelfia.person.gruppo.store", $persona->id) }}"
      >
        @csrf
        <input type="hidden" name="torna_indietro" value="1" />
        @if ($gruppoAttuale)
          <input
            type="hidden"
            name="current_data_entrata"
            value="{{ $gruppoAttuale->pivot->data_entrata_gruppo }}"
          />
          <p class="text-body-secondary">
            {{ $persona->nome }} lascia il gruppo
            <strong>{{ $gruppoAttuale->nome }}</strong>
            il giorno in cui entra nel nuovo.
          </p>
        @endif

        <div class="mb-3">
          <label class="form-label" for="modal-gruppo-familiare-gruppo">
            Gruppo familiare
          </label>
          <select
            class="form-select"
            name="{{ $gruppoAttuale ? "new_gruppo_id" : "gruppo_id" }}"
            id="modal-gruppo-familiare-gruppo"
            required
          >
            <option value="">Scegli il gruppo</option>
            @foreach ($gruppiFamiliari as $gruppo)
              @continue($gruppo->id === $gruppoAttuale?->id)
              <option value="{{ $gruppo->id }}">{{ $gruppo->nome }}</option>
            @endforeach
          </select>
        </div>

        <label class="form-label" for="modal-gruppo-familiare-data">
          Data di entrata nel gruppo
        </label>
        <input
          class="form-control"
          type="date"
          name="{{ $gruppoAttuale ? "new_data_entrata" : "data_entrata" }}"
          id="modal-gruppo-familiare-data"
          value="{{ $oggi }}"
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
      <button class="btn btn-success" form="form-modal-gruppo-familiare">
        {{ $gruppoAttuale ? "Sposta" : "Aggiungi" }}
      </button>
    </x-slot:footer>
  </x-modal>

  <x-modal modal-id="modal-incarico" modal-title="Aggiungi un incarico">
    <x-slot:body>
      <form
        method="POST"
        id="form-modal-incarico"
        action="{{ route("nomadelfia.persone.incarichi.assegna", $persona->id) }}"
      >
        @csrf
        <div class="mb-3">
          <label class="form-label" for="modal-incarico-incarico">
            Incarico
          </label>
          <select
            class="form-select"
            name="incarico_id"
            id="modal-incarico-incarico"
            required
          >
            <option value="">Scegli l'incarico</option>
            @foreach ($incarichiAssegnabili as $incarico)
              <option value="{{ $incarico->id }}">{{ $incarico->nome }}</option>
            @endforeach
          </select>
        </div>

        <label class="form-label" for="modal-incarico-data">
          Data di inizio
        </label>
        <input
          class="form-control"
          type="date"
          name="data_inizio"
          id="modal-incarico-data"
          value="{{ $oggi }}"
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
      <button class="btn btn-success" form="form-modal-incarico">
        Aggiungi
      </button>
    </x-slot:footer>
  </x-modal>
@endcan

@can("popolazione.persona.modifica")
  <x-modal modal-id="modal-azienda" modal-title="Aggiungi un'azienda">
    <x-slot:body>
      <form
        method="POST"
        id="form-modal-azienda"
        action="{{ route("nomadelfia.persone.aziende.assegna", ["idPersona" => $persona->id]) }}"
      >
        @csrf
        <input type="hidden" name="torna_indietro" value="1" />
        <div class="mb-3">
          <label class="form-label" for="modal-azienda-azienda">Azienda</label>
          <select
            class="form-select"
            name="azienda_id"
            id="modal-azienda-azienda"
            required
          >
            <option value="">Scegli l'azienda</option>
            @foreach ($aziendeAssegnabili as $azienda)
              <option value="{{ $azienda->id }}">
                {{ $azienda->nome_azienda }}
              </option>
            @endforeach
          </select>
        </div>

        <fieldset class="mb-3">
          <legend class="form-label fs-6">Mansione</legend>
          <div class="row row-cols-1 row-cols-sm-2 g-2">
            @foreach ([App\Nomadelfia\Azienda\Models\Azienda::MANSIONE_LAVORATORE => ["Lavoratore", "Lavora nell'azienda"], App\Nomadelfia\Azienda\Models\Azienda::MANSIONE_RESPONSABILE => ["Responsabile", "Guida l'azienda"]] as $mansione => [$nome, $spiegazione])
              <div class="col">
                <input
                  class="btn-check"
                  type="radio"
                  name="mansione"
                  id="modal-azienda-{{ Illuminate\Support\Str::slug($mansione) }}"
                  value="{{ $mansione }}"
                  @checked($loop->first)
                />
                <label
                  class="profile-tipo"
                  for="modal-azienda-{{ Illuminate\Support\Str::slug($mansione) }}"
                >
                  <span class="fw-semibold">{{ $nome }}</span>
                  <small>{{ $spiegazione }}</small>
                </label>
              </div>
            @endforeach
          </div>
        </fieldset>

        <label class="form-label" for="modal-azienda-data"
          >Data di inizio</label
        >
        <input
          class="form-control"
          type="date"
          name="data_inizio"
          id="modal-azienda-data"
          value="{{ $oggi }}"
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
      <button class="btn btn-success" form="form-modal-azienda">
        Aggiungi
      </button>
    </x-slot:footer>
  </x-modal>
@endcan
